/** @format */

let dateFieldIndex = 0;

function addDateField() {
  const container = document.getElementById("holiday_dates_container");
  const newField = document.createElement("div");
  newField.className = "date-field";
  newField.style.marginBottom = "20px";
  newField.innerHTML =
    '<label for="holiday_date_' +
    dateFieldIndex +
    '">Holiday Date:</label>' +
    '<input type="date" id="holiday_date_' +
    dateFieldIndex +
    '" name="holiday_dates[]" />' +
    '<button type="button" class="button remove" style="margin-left: 10px;" onclick="removeDateField(this)">Remove</button>';
  container.appendChild(newField);
  dateFieldIndex++;
}

function removeDateField(button) {
  const field = button.parentElement;
  field.parentElement.removeChild(field);
}

function copyToClipboard(text) {
  var tempInput = document.createElement("input");
  tempInput.style.position = "absolute";
  tempInput.style.left = "-9999px";
  tempInput.value = text;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
  alert("Shortcode copied to clipboard");
}
