$(function(){
  var $form = $('#mainForm');

  $form.find('#inputFile').on('change', function() {
    var file = this.files[0];
    if (file != null) {
      var detectTypeSelected = $form.find('#detectTypeSelectBox').val();
      if (detectTypeSelected) {
        $('#submitButton').prop("disabled", false);
      }
    }
  });

  $form.on('submit', function() {
    $(this).find('button[type="submit"]').attr('disabled', 'disabled');
  });

  var $helpPanel = $('.helpPanel');
  $helpPanel.find('a.help').on('click', function(e) {
    e.preventDefault();

    var $helpContent = $('.helpContent');
    $helpContent.toggle();
  })
});
