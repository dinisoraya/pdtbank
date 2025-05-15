let showing = false;
function toggleBalance() {
  const balanceElement = document.getElementById("balance");
  const eyeIcon = document.getElementById("eye-icon");

  if (!showing) {
    balanceElement.innerText = balanceElement.dataset.realBalance;
    eyeIcon.classList.remove("bi-eye");
    eyeIcon.classList.add("bi-eye-slash");
  } else {
    balanceElement.innerText = "••••••••";
    eyeIcon.classList.remove("bi-eye-slash");
    eyeIcon.classList.add("bi-eye");
  }

  showing = !showing;
}