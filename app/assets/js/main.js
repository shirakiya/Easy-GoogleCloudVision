$(function(){
  $form = $('#mainForm');
  $form.find('#inputFile').on('change', function() {
    var file = this.files[0];
    if (file != null) {
      var detectTypeSelected = $form.find('#detectTypeSelectBox').val();
      if (detectTypeSelected) {
        $('#submitButton').prop("disabled", false);
      }
    }
  });
});
