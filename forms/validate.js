document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.php-email-form');

  form.addEventListener('submit', function() {
      e.preventDefault(); // Prevent the default form submission behavior

      // Get form input fields
      const nameInput = form.querySelector('#name');
      const emailInput = form.querySelector('#email');
      const subjectInput = form.querySelector('#subject');
      const messageInput = form.querySelector('textarea');

      // Validate name field
      if (nameInput.value.trim() === '') {
          displayError('Please enter your name');
          return;
      }

      // Validate email field
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(emailInput.value.trim())) {
          displayError('Please enter a valid email address');
          return;
      }

      // Validate subject field
      if (subjectInput.value.trim() === '') {
          displayError('Please enter a subject');
          return;
      }

      // Validate message field
      if (messageInput.value.trim() === '') {
          displayError('Please enter a message');
          return;
      }

      // If all fields are valid, submit the form
      form.submit();
  });

  function displayError(message) {
      // Display error message
      const errorMessageElement = form.querySelector('.error-message');
      errorMessageElement.textContent = message;
      errorMessageElement.classList.add('d-block');

      // Hide other messages
      form.querySelector('.sent-message').classList.remove('d-block');
  }
});
