// This code allows a user to interact with a RESTful API.
// The user can select a resource, a request method, and a request body.
// The code then sends the request to the server and displays the response.

// The following functions are used to interact with the API:

// `alterListofResource()`: Toggles the visibility of the resource list.
// `RequestMethod()`: Sets the request method and triggers necessary actions.
// `populatelistopt()`: Updates the options in the datalist for resources.
// `settingResourceURL()`: Sets the resource URL.
// `setRequestBodyValue()`: Sets the request body.
// `forwardRequest()`: Retrieves the response from the server.
// `clearingResponse()`: Clears the response and reloads the page.

// The following variables are used to store the request information:

// `requestMethod`: The request method.
// `resourceURL`: The resource URL.
// `requestBody`: The request body.


// Toggles the visibility of the resource list
const alterListofResource = () => {
document.getElementById("resourceList").classList.toggle("toggle-data");
};
// Sets the request method and triggers necessary actions
const RequestMethod = (e) => {
requestMethod = e;
populatelistopt();
showRequestBody();
};
// Updates the options in the datalist for resources
const populatelistopt = () => {
const resourcesEl = document.getElementById("resources");
document.getElementById("resource").value = "";
const resources = [
"https://student.csc.liv.ac.uk/~sgpgezah/v1/Rest.php?resource=teams",
"https://student.csc.liv.ac.uk/~sgpgezah/v1/Rest.php?resource=players",
"https://student.csc.liv.ac.uk/~sgpgezah/v1/Rest.php?resource=add-player",
"https://student.csc.liv.ac.uk/~sgpgezah/v1/Rest.php?resource=delete-player",
"https://student.csc.liv.ac.uk/~sgpgezah/v1/Rest.php?resource=update-player&team_id=<team_id>&id=<player_id>",
"https://student.csc.liv.ac.uk/~sgpgezah/v1/Rest.php?resource=players&team_id=<team_id>&player_id=<player_id>"
];
resources.forEach((resource) => {
const option = document.createElement("option");
option.value = resource;
resourcesEl.appendChild(option);
});
};
// Sets the resource URL
const settingResourceURL = (e) => {
resourceURL = e;
};
// Sets the request body
const setRequestBodyValue = (e) => {
requestBody = e;
};
// Retrieves the response from the server
const forwardRequest = () => {
const xhr = new XMLHttpRequest();
xhr.onreadystatechange = function () {
if (this.readyState === 4) {
if (document.getElementById("statusCode")) {
document.getElementById(
"statusCode"
).innerHTML = `HTTP Status Code: ${this.status} ${this.statusText}<br><br>`;
}
if (this.status === 200) {
try {
const response = JSON.parse(this.responseText);
const responseBody = JSON.stringify(response, null, 2)
.replace(/</g, "&lt;")
.replace(/>/g, "&gt;")
.replace(/\n/g, "<br>")
.replace(/\s/g, "&nbsp;");
if (document.getElementById("responseBody")) {
document.getElementById(
"responseBody"
).innerHTML = `HTTP Response Body:${responseBody}`;
document.getElementById("responseBody").style.display = "block";
console.log("For a new request press the Field Clearance button");
}
} catch (error) {
if (document.getElementById("responseBody")) {
document.getElementById(
"responseBody"
).innerHTML = `${this.responseText}`;
document.getElementById("responseBody").style.display = "block";
}
}
} else {
if (document.getElementById("responseBody")) {
document.getElementById(
"responseBody"
).innerHTML = `Error: ${this.responseText}`;
document.getElementById("responseBody").style.display = "none";
}
}
}
};
const method = document.getElementById("method").value;
const resource = document.getElementById("resource").value;
xhr.open(method, resource, true);
if (method === "GET") {
xhr.send();
} else {
xhr.setRequestHeader("Content-Type", "application/json");
xhr.send(requestBody);
}
};
// Clears the response and reloads the page
const clearingResponse = () => {
location.reload();
};
let requestMethod = "";
let resourceURL = "";
let requestBody = "";