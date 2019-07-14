function taskContainsDigit(taskNameString, element) {
  if (taskNameString.match(/\d+/) != null) {
    element.classList.add('have-digit')
  }
  else element.classList.remove('have-digit')
}

function refreshTaskContainsDigit() {
  $('table input').each( (i,e) => taskContainsDigit(e.value, e))
}

function checkForDuplicates(editedElement = '#taskName') {
  let haveDuplicates = false

  $('#tasksTable input').each( (i,v) => {
    if (v.value == $(editedElement).val() && $(editedElement).attr('id') != v.id ) {
      $(v).parent().addClass('duplicate')
      haveDuplicates = true
    } else $(v).parent().removeClass('duplicate')
  })
  return haveDuplicates
}

$(document).ready(function() {
  refreshTaskContainsDigit()
  $('table input').on('input', e => taskContainsDigit(e.target.value, e.target))
});

  // document.querySelector('#taskName').addEventListener('input', e => {
//   taskContainsDigit(e.target.value, e.target)
// })