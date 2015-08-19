// will be called when the window loads
window.onload = loadElements;
var userJson;
var usrChart;
// prepares all necessary elements and fetches data
function loadElements(){
    $('.datepicker').datepicker();
    $('[data-toggle="popover"]').popover();
    $.getJSON('request.php', processInfo);
}
// called whenever a change or addition is made
function refreshData(){
    $.getJSON('request.php', processUpdate);
}
function processUpdate(update){
    $('#usersTable tr').remove();
    jsonTable(update.users);
    usrChart.segments[0].value = update.statistics[0];
    usrChart.segments[1].value = update.statistics[1];
    usrChart.segments[2].value = update.statistics[2];
    usrChart.update();
}
function deleteUser(){
    $.ajax({
        type: 'DELETE',
        url: 'change.php',
        data: {userid: $('#useridChg').val()},
        success: refreshData
    });
    $('#changeUserModal').modal('hide');
}
// takes in JSON response and sends to where it's needed
function processInfo(response){
    userChart(response.statistics);
    jsonTable(response.users);
    userJson = response.users;
}
// adds the data to chart and displays it
function userChart(statistics){
    var stats = [{
        value: statistics[0],
        color: '#009933',
        highlight: '#66C266',
        label: 'Enabled'
    },
    {
        value: statistics[1],
        color: '#C21418',
        highlight: '#EC4A4F',
        label: 'Expired'
    },
    {
        value: statistics[2],
        color: '#444444',
        highlight: '#777777',
        label: 'Disabled'
    }]
    var ctx = $('#userChart').get(0).getContext('2d');
    usrChart = new Chart(ctx).Pie(stats, {responsive: true});
}
// handles the checkbox for enabling user expiration
function toggleExpire(){
    if (document.getElementById('chkExpire').checked){
        $('#expireInput').removeAttr('disabled');
    }
    else{
        $('#expireInput').attr('disabled', 'true');
        $('#expire').val('');
    }
}

function toggleExpireChg(){
    if (document.getElementById('chkExpireChg').checked){
        $('#expireInputChg').removeAttr('disabled');
    }
    else{
        $('#expireInputChg').attr('disabled', 'true');
        $('#expireChg').val('');
    }
}
// validates changes and POSTs data to change.php
function changeUser(){
    var acctStatus = '0';
    var expiryDate = null;
    var passwd;
    // accepts only valid ISO 8601 dates
    var valiDate = /^\d{4}-[01]\d-[0-3]\d$/;
    // perform data formatting
    if(document.getElementById('statusChg').checked){
        acctStatus = '1';
    }
    else{
        acctStatus = '0';
    }
    if(document.getElementById('chkExpireChg').checked){
        var expiryDate = $('#expireChg').val();
    }
    else{
        var expiryDate = null;
    }
    // perform data validation (validated on backend too)
    if(valiDate.test(expiryDate) == false && expiryDate != null){
        alert('Expiration date not valid');
    }
    else if($('#password').val() != $('#repeatpw').val()){
        alert('Password and repeated password do not match');
    }
    // if all data checks out, POST to insert.php
    else{
        var user = {
            userid: $('#useridChg').val(),
            username: $('#usernameChg').val(),
            fname: $('#fnameChg').val(),
            lname: $('#lnameChg').val(),
            expire: expiryDate,
            status: acctStatus,
            password: $('#passwordChg').val(),
            repeat: $('#repeatpwChg').val(),
        }
        $.post('change.php', user, refreshData);
        $('#changeUserModal').modal('hide');
    }
}
// validates form and POSTs data to insert.php
function createUser(){
    var acctStatus = '0';
    var expiryDate = null;
    var passwd;
    // accepts only valid ISO 8601 dates
    var valiDate = /^\d{4}-[01]\d-[0-3]\d$/;
    // perform data formatting
    if(document.getElementById('status').checked){
        acctStatus = '1';
    }
    else{
        acctStatus = '0';
    }
    if(document.getElementById('chkExpire').checked){
        var expiryDate = $('#expire').val();
    }
    else{
        var expiryDate = null;
    }
    // perform data validation (validated on backend too)
    if(valiDate.test(expiryDate) == false && expiryDate != null){
        alert('Expiration date not valid');
    }
    else if($('#password').val() == '' || $('#repeatpw').val() == ''){
        alert('You must set a password and repeat it');
    }
    else if($('#password').val() != $('#repeatpw').val()){
        alert('Password and repeated password do not match');
    }
    // if all data checks out, POST to insert.php
    else{
        var user = {
            username: $('#username').val(),
            fname: $('#fname').val(),
            lname: $('#lname').val(),
            expire: expiryDate,
            status: acctStatus,
            password: $('#password').val(),
            repeat: $('#repeatpw').val(),
        }
        $.post('insert.php', user, refreshData);
    }
}
function findUser(userid){
    for(var i = 0; i < userJson.length; i++){
        if(userid == userJson[i].userid){
            return userJson[i];
        }
    }
}
function userChange(userid){
    var chgUser = findUser(userid);
    $('#useridChg').val(userid);
    $('#usernameChg').val(chgUser.username);
    $('#fnameChg').val(chgUser.fname);
    $('#lnameChg').val(chgUser.lname);
    $('#passwordChg').val('');
    $('#repeatpwChg').val('');
    if(chgUser.expiration == null){
        document.getElementById('chkExpireChg').checked = false;
        $('#expireChg').val('');
        $('#expireInputChg').attr('disabled', 'true');
    }
    else{
        document.getElementById('chkExpireChg').checked = true;
        $('#expireChg').val(chgUser.expiration);
        $('#expireInputChg').removeAttr('disabled');
    }
    if(chgUser.disabled == 1){
        document.getElementById('statusChg').checked = true;
    }
    else{
        document.getElementById('statusChg').checked = false;
    }
    $('#changeUserModal').modal({backdrop: false});
    $('#expireChg').datepicker();
}
function jsonTable(users){
    var table = document.getElementById('usersTable');
    // iterates through the user array
    for(var i = 0; i < users.length; i++) {
        var user = users[i];
        var newRow = table.insertRow();
        // iterates through the users details
        for(var detail in user){
            var newCell = newRow.insertCell();
            // special case for disabled value
            if(detail != 'disabled'){
                var newText = document.createTextNode(user[detail]);
            }
            else{
                if(user.disabled == 0){
                    var newText = document.createTextNode('False');
                }
                else{
                    var newText = document.createTextNode('True');
                }
            }
            newCell.appendChild(newText);
        }
        // adds the "edit" button
        var newCell = newRow.insertCell();
        var userButton = document.createElement('button');
        userButton.textContent = 'Edit';
        userButton.className = 'btn btn-default btn-sm';
        userButton.userID = user.userid;
        userButton.onclick = function(){userChange(this.userID)};
        newCell.appendChild(userButton);
    }
}