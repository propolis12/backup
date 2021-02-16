export function initializeDropzone() {
  var formElement = document.querySelector('.js-reference-dropzone');
  if(!formElement) {
      return;
  }
  var dropzone = new Dropzone(formElement,{
      paramName:'reference'
  });

}