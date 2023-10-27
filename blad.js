window.addEventListener('error', function(event) {
    var errorMessage = event.message;
    var errorContainer = document.getElementById('error-container');
    console.log(errorMessage)
    window.location.href = 'blad.html';
  });