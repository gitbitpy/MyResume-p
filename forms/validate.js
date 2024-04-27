const form = document.querySelector("form[class='php-email-form']");
const nameInput = document.querySelector("input[name='name']");
const emailInput = document.querySelector("input[name='email']");
const subjectInput = document.querySelector("input[name='subject']");
const messageInput = document.querySelector("textarea[name='message']");

nameInput.isValid = () => !!nameInput.value;
emailInput.isValid = () => isValidEmail(emailInput.value);
subjectInput.isValid = () => !!subjectInput.value;
/**phoneInput.isValid = () => isValidPhone(phoneInput.value);*/
messageInput.isValid = () => !!messageInput.value;

const inputFields = [nameInput, emailInput, subjectInput, messageInput];

const isValidEmail = (email) => {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

/**const isValidPhone = (phone) => {
  const re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
  return re.test(String(phone).toLowerCase());
};*/

let shouldValidate = false;
let isFormValid = false;

const validateInputs = () => {
  if (!shouldValidate) return;

  isFormValid = true;
  inputFields.forEach((input) => {
    input.classList.remove("invalid");
    input.nextElementSibling.classList.add("hide");

    if (!input.isValid()) {
      input.classList.add("invalid");
      isFormValid = false;
      input.nextElementSibling.classList.remove("hide");
    }
  });
};

form.addEventListener("submit", (e) => {
    e.preventDefault();
    form.querySelector('.loading').classList.add('d-block');
    
    // Flag to determine if validation should occur
    let shouldValidate = true;
    
    // Validate form inputs
    validateInputs();
    
    if (isFormValid) {
        // Serialize form data
        const formData = new FormData(form);
        
        // Perform AJAX request
        fetch('contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Response is OK, show success message
                form.querySelector('.sent-message').classList.add('d-block');
                form.querySelector('.loading').classList.remove('d-block');
                form.reset(); // Reset the form
            } else {
                // Response is not OK, show error message
                throw new Error('Failed to submit form');
            }
        })
        .catch(error => {
            // Handle errors
            console.error('Error:', error);
            form.querySelector('.error-message').textContent = 'Failed to submit form';
            form.querySelector('.error-message').classList.add('d-block');
        });
    }
});

inputFields.forEach((input) => input.addEventListener("input", validateInputs));