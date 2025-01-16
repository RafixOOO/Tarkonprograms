window.addEventListener('error', function(event) {
   var errorLineNumber = event.lineno;
    var errorMessage = event.message;
    $.ajax({
      url: 'http://10.100.101.14/programs/Tarkonprograms/saveblad.php',
      type: 'POST',
      data: { errorMessage: errorMessage+' | Wiersz: '+errorLineNumber },
      success: function(response) {
          console.log('Dane wysłane do PHP:', response);
          window.location.href = 'http://10.100.101.14/programs/Tarkonprograms/blad.html';
      },
      error: function(xhr, status, error) {
          console.error('Błąd:', error);
      }
  });
  });