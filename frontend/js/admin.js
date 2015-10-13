// will be called when the window loads
window.onload = loadElements;
var adminJson;
// prepares all necessary elements and fetches data
function loadElements(){
    $.getJSON('request_admin.php', processInfo);
}
// called whenever a change or addition is made
function refreshData(){
    $.getJSON('request_admin.php', processUpdate);
}
function processUpdate(update){
    $('#adminsTable tr').remove();
    adminJson = update.admins;
    jsonTable(update.admins);
}
function deleteAdmin(){
    $.ajax({
        type: 'DELETE',
        url: 'change_admin.php',
        data: {usernameOld: $('#usernameOld').val()},
        success: refreshData
    });
    $('#changeAdminModal').modal('hide');
}
// takes in JSON response and sends to where it's needed
function processInfo(response){
    jsonTable(response.admins);
    adminJson = response.admins;
}

// validates changes and POSTs data to change_admin.php
function changeAdmin(){
    // perform data validation (validated on backend too)
    if($('#usernameChg').val().trim() == ''){
        alert('Username cannot be blank');
    }
    else if($('#password').val() != $('#repeatpw').val()){
        alert('Password and repeated password do not match');
    }
    // if all data checks out, POST to change.php
    else{
        var user = {
            usernameOld: $('#usernameOld').val().trim(),
            username: $('#usernameChg').val(),
            password: $('#passwordChg').val(),
            repeat: $('#repeatpwChg').val()
        }
        $.post('change_admin.php', user, refreshData);
        $('#changeAdminModal').modal('hide');
    }
}
// validates form and POSTs data to insert.php
function createAdmin(){
    // perform data validation (validated on backend too)
    if($('#username').val().trim() == ''){
        alert('You must set a username');
    }
    else if($('#password').val().trim() == '' || $('#repeatpw').val().trim() == ''){
        alert('You must set a password and repeat it');
    }
    else if($('#password').val() != $('#repeatpw').val()){
        alert('Password and repeated password do not match');
    }
    // if all data checks out, POST to insert.php
    else{
        var user = {
            username: $('#username').val().trim(),
            password: $('#password').val(),
            repeat: $('#repeatpw').val()
        }
        $.post('insert_admin.php', user, refreshData);
    }
}
function findAdmin(username){
    for(var i = 0; i < adminJson.length; i++){
        if(username == adminJson[i].username){
            return adminJson[i];
        }
    }
}
function adminChange(username){
    $('#usernameOld').val(username);
    $('#usernameChg').val(username);
    $('#passwordChg').val('');
    $('#repeatpwChg').val('');
    $('#changeAdminModal').modal({backdrop: false});
}
function jsonTable(admins){
    var table = document.getElementById('adminsTable');
    // iterates through the admin array
    for(var i = 0; i < admins.length; i++) {
        var admin = admins[i];
        var newRow = table.insertRow();
        var newCell = newRow.insertCell();
        var newText = document.createTextNode(admin);
        newCell.appendChild(newText);
        // adds the "edit" button
        newCell = newRow.insertCell();
        var adminButton = document.createElement('button');
        adminButton.textContent = 'Edit';
        adminButton.className = 'btn btn-default';
        adminButton.username = admin;
        adminButton.onclick = function(){adminChange(this.username)};
        newCell.appendChild(adminButton);
    }
}