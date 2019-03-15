jQuery.validator.setDefaults({
  debug: true,
  success: "valid"
});
jQuery( "#bbp_topic_title" ).validate({
  rules: {
    field: {
      required: true,
      minlength: 10
    }
  }
});