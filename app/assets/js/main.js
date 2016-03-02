$(function(){
  $('#inputFile').on('change', function() {
    var file = this.files[0];
    if (file != null) {
      $('#submitButton').prop("disabled", false);
    }
  });
});
