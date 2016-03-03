$(function(){
  $form = $('#mainForm');
  $form.find('#inputFile').on('change', function() {
    var file = this.files[0];
    if (file != null) {
      var detectTypeCheck = $form.find('input[name=detectType]:checked').val();
      if (detectTypeCheck) {
        $('#submitButton').prop("disabled", false);
      }
    }
  });
});
