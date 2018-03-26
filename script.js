/**
 * Created by Sebbans on 2018-01-02.
 */


var mouseX = 0;
var mouseY = 0;
var mousePos = [mouseX, mouseY];


function logout(){
    $.ajax({
        type: "POST",
        url: "doSomething.php",
        data: "action=logout",
        success: function (response) {
            //$("#text").html(response);
            console.log(response);
            $("#login").html("<form method='post'  id='loginform' enctype='multipart/form-data'><input type='text' name='username' id='username' placeholder='Username'><input type='password' name='password' id='password' placeholder='Password'><button type='submit' name='login'>login</button><input type='hidden' name='action' value='login'></form>");
            //window.location.href = "index.php";
            updateStats();


            $("#loginform").validate({
                rules: {
                    password: {
                        required: true
                    }
                },
                messages:{
                    password:{
                        required: "please enter the password"
                    }
                },
                submitHandler: submitLogin
            });

        }
    });
}

function submitLogin(){
    var data = $("#loginform").serialize();
    $.ajax({
        type: "POST",
        url: "doSomething.php",
        data: data,
        success: function(response){
            //$("#text").html(response);
            console.log(response);
            if(response === "ok."){
                //$("#text").html("Welcome.");
                $("#login").load("includes/login.php");
                updateStats();
                /*$.ajax({
                    url: "includes/login.php",
                    success: function(result){
                        console.log(result);
                        $("#login").html(result);
                    }
                });*/
                //$("#login").html("<h3 id='logout' onclick='logout()'>Logout</h3>");
                //updateStats();
                //window.location.href = "index.php";
                //alert("Welcome User.");
            }else{
                alert("Wrong info on login. Username or password is incorrect.");
            }


            /*if(response == "ok."){
             $("#text").html(response);
             }else{
             $("#text").html("no.");
             }*/
        }
    });
}

function submitCreateUser(){
    var data = $("#createform").serialize();
    $.ajax({
        type: "POST",
        url: "doSomething.php",
        data: data,
        success: function(response){
            //$("#text").html(response);
            console.log(response);
            if(response === "ok."){
                //alert("Welcome User.");
                $('#createform')[0].reset();
                $("#login").html("<h3 id='logout' onclick='logout()'>Logout</h3>");
                //window.location.href = "index.php";
                updateStats();
            }else{
                $('#createform')[0].reset();
                alert("Username Taken.");
            }


            /*if(response == "ok."){
             $("#text").html(response);
             }else{
             $("#text").html("no.");
             }*/
        }
    });
}

function displayStats(){
    var data = new FormData();

    data.append("action", "displaystats");

    $.ajax({
        type: "POST",
        url: "doSomething.php",
        contentType: false,
        processData: false,
        data: data,
        success: function(response){

            $("#statstext").html(response);
            $("#stats").toggle();

            //alert(response);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        }
    });
}

function updateStats(){
    var data = new FormData();

    data.append("action", "displaystats");


    $.ajax({
        type: "POST",
        url: "doSomething.php",
        contentType: false,
        processData: false,
        data: data,
        success: function(response){
            $("#statstext").html(response);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        }
    });
}

function updateClicks(){
    var data = new FormData();
    data.append("action", "displayclicks");
    data.append("height", $( "#target" ).height());

    $.ajax({
        type: "POST",
        url: "doSomething.php",
        contentType: false,
        processData: false,
        data: data,
        success: function(response){
            $("#clicks").html(response);
            //console.log(response);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        }
    });
}



function colorUpdate(jscolor){
    //console.log('#' + jscolor);
    //alert("yup");
    var data = new FormData();
    data.append("action", "updateColor");
    data.append("color", jscolor);

    $.ajax({
        type: "POST",
        url: "doSomething.php",
        contentType: false,
        processData: false,
        data: data,
        success: function(response){
            //$("#clicks").html(response);
            console.log('#' + response);
            updateClicks();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        }
    });
}

function showAccountCreation(toggle){
    console.log($("#createAccount"));
    $("#createAccount").show();
}



$(document).ready(function() {
    updateClicks();
    $("#loginform").validate({
        rules: {
            username: {
                required: true
            },
            password: {
                required: true
            }
        },
        messages:{
            username: {
                required: "Please enter a username"
            },
            password:{
                required: "please enter a password"
            }
        },
        submitHandler: submitLogin
    });

    $("#createform").validate({
        rules: {
            username: {
                required: true
            },
            password: {
                required: true
            }
        },
        messages:{
            username: {
                required: "Please enter a username"
            },
            password:{
                required: "please enter a password"
            }
        },
        submitHandler: submitCreateUser
    });


    /*$("#logout").click(function () {
        $.ajax({
            type: "POST",
            url: "doSomething.php",
            data: "action=logout",
            success: function (response) {
                //$("#text").html(response);
                alert(response);
                window.location.href = "index.php";
            }
        });
    });*/

    /* On key up: */
    $(document).keyup(function(e) {

        switch(e.which){
            case 27:
                displayStats();
                break;
            case 67:
                showAccountCreation(true);
                break;

            case undefined:
                break;
            default:
                console.log("key pressed: " + e.which);
                break;
        }
    });


    $( "#target" ).click(function() {
        var color = "#" + $("#jscolor").val();
        //alert("Wow click position: "+mousePos);
        $("#clicks").append("<div id='click' style='top:" + ($( "#target" ).height() - mousePos[1]) + "; left:" + mousePos[0] + "; border-color:" + color + "'></div>");


        var data = new FormData();

        data.append("action", "clicked");
        data.append("mousex", mousePos[0]);
        data.append("mousey", mousePos[1]);

        $.ajax({
            type: "POST",
            url: "doSomething.php",
            contentType: false,
            processData: false,
            data: data,
            success: function(response){
                console.log(response);
            }
        });

        updateStats();

    });

    $( "#target" ).mousemove(function( event ) {
        mouseX = event.pageX;
        mouseY = $( "#target" ).height() - event.pageY;
        mousePos = [mouseX, mouseY];
        var msg = "Handler for .mousemove() called at ";
        msg += event.pageX + ", " + ($( "#target" ).height() - event.pageY);
        msg += "and the element is " + $( "#target" ).width() + " x " + $( "#target" ).height();
        //console.log(msg);
        //$( "#log" ).append( "<div>" + msg + "</div>" );
    });

    $( window ).resize(function() {
        updateClicks();

    });


});