/** @format */

let hcpt__dateFieldIndex = 0;

function hcpt__addDateField() {
  const container = document.getElementById("hcpt__holiday_dates_container");
  const newField = document.createElement("div");
  newField.className = "hcpt__date-field";
  newField.style.marginBottom = "20px";
  newField.innerHTML =
    `<label for="hcpt__holiday_date_${hcpt__dateFieldIndex}">Holiday Date:</label>` +
    `<input type="date" id="hcpt__holiday_date_${hcpt__dateFieldIndex}" name="hcpt__holiday_dates[]" />` +
    `<button type="button" class="hcpt__button hcpt__remove" style="margin-left: 10px;" onclick="hcpt__removeDateField(this)">Remove</button>`;
  container.appendChild(newField);
  hcpt__dateFieldIndex++;
}

function hcpt__removeDateField(button) {
  const field = button.parentElement;
  field.parentElement.removeChild(field);
}

function hcpt__copyToClipboard(text) {
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
