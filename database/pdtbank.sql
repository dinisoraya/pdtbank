-- pdtbank Database Schema
-- This script creates the database schema for the pdtbank system.

-- users table
-- This table stores user information.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,        
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- accounts table
-- This table stores user accounts and their balances.
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    balance DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- transactions table
-- This table stores all transactions made by users.
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id CHAR(36) NOT NULL UNIQUE,
    from_account VARCHAR(20) NOT NULL,
    to_account VARCHAR(20) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- get_balance function
-- This function retrieves the balance of a given account number.
DELIMITER $$ 
CREATE FUNCTION get_balance(p_account VARCHAR(20))
RETURNS DECIMAL(15,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_balance DECIMAL(15,2);
    SELECT balance INTO v_balance
    FROM accounts
    WHERE account_number = p_account;
    RETURN IFNULL(v_balance, 0);
END$$
DELIMITER ;

-- validate_transaction trigger
-- This trigger validates the transaction before inserting it into the transactions table.
DELIMITER $$  
CREATE TRIGGER validate_transaction
BEFORE INSERT ON transactions
FOR EACH ROW
BEGIN
    IF NEW.from_account <> 'Cash Deposit ATM' THEN
        IF NOT EXISTS (SELECT 1 FROM accounts WHERE account_number = NEW.from_account) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Sender account not found',
                MYSQL_ERRNO = 1641;
        END IF;
    END IF;

    IF NEW.to_account = 'Cash Deposit ATM' THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot deposit to system account',
            MYSQL_ERRNO = 1642;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM accounts WHERE account_number = NEW.to_account) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Recipient account not found',
            MYSQL_ERRNO = 1643;
    END IF;

    IF NEW.from_account = NEW.to_account THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Cannot transfer to the same account',
            MYSQL_ERRNO = 1644;
    END IF;

    IF EXISTS (SELECT 1 FROM transactions WHERE transaction_id = NEW.transaction_id) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Duplicate transaction',
            MYSQL_ERRNO = 1645;
    END IF;

    IF NEW.amount <= 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Amount must be greater than zero',
            MYSQL_ERRNO = 1646;
    END IF;
END$$
DELIMITER ;

-- transfer_money procedure
-- This procedure handles the transfer of money between accounts.
DELIMITER $$
CREATE PROCEDURE transfer_money(
    IN p_transaction_id CHAR(36),
    IN p_from_account VARCHAR(20),
    IN p_to_account VARCHAR(20),
    IN p_amount DECIMAL(15,2)
)
BEGIN
    DECLARE v_balance DECIMAL(15,2);

    SET v_balance = get_balance(p_from_account);
    
    IF v_balance < p_amount THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Insufficient balance',
            MYSQL_ERRNO = 1647;
    END IF;

    INSERT INTO transactions (transaction_id, from_account, to_account, amount)
    VALUES (p_transaction_id, p_from_account, p_to_account, p_amount);

    UPDATE accounts
    SET balance = balance - p_amount
    WHERE account_number = p_from_account;

    UPDATE accounts
    SET balance = balance + p_amount
    WHERE account_number = p_to_account;
END$$
DELIMITER ;

-- deposit_money procedure
-- This procedure handles the deposit of money into an account.
DELIMITER $$ 
CREATE PROCEDURE deposit_money(
    IN p_transaction_id CHAR(36),
    IN p_to_account VARCHAR(20),
    IN p_amount DECIMAL(15,2)
)
BEGIN
    INSERT INTO transactions (transaction_id, from_account, to_account, amount)
    VALUES (p_transaction_id, 'Cash Deposit ATM', p_to_account, p_amount);

    UPDATE accounts
    SET balance = balance + p_amount
    WHERE account_number = p_to_account;
END$$
DELIMITER ;

-- get_transaction_history procedure
-- This procedure retrieves the transaction history for a given account number.
DELIMITER //

CREATE PROCEDURE get_transaction_history(
    IN p_account VARCHAR(20)
)
BEGIN
    DECLARE v_account VARCHAR(20) DEFAULT p_account;

    SELECT
        t.created_at,
        CASE
            WHEN t.from_account = v_account THEN 
                CONCAT('Transfer to ', t.to_account, ' – ', COALESCE(u_to.username, 'Unknown'))
            WHEN LOWER(t.from_account) = 'cash deposit atm' THEN 
                'Cash Deposit ATM'
            ELSE 
                CONCAT('Received from ', t.from_account, ' – ', COALESCE(u_from.username, 'Unknown'))
        END AS description,
        CASE
            WHEN t.from_account = v_account THEN -t.amount
            ELSE t.amount
        END AS net_change,
        SUM(
            CASE 
                WHEN t.from_account = v_account THEN -t.amount 
                ELSE t.amount 
            END
        ) OVER (
            ORDER BY t.created_at
            ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
        ) AS balance_at_that_time
    FROM transactions AS t
    LEFT JOIN accounts AS a_from ON t.from_account = a_from.account_number
    LEFT JOIN users    AS u_from ON a_from.user_id = u_from.id
    LEFT JOIN accounts AS a_to   ON t.to_account   = a_to.account_number
    LEFT JOIN users    AS u_to   ON a_to.user_id   = u_to.id
    WHERE t.from_account = v_account
       OR t.to_account   = v_account
    ORDER BY t.created_at ASC;
END //
DELIMITER ;