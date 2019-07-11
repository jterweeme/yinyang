/*
Jasper ter Weeme 2019
*/

function allowDrop(ev) {
    //debugger;
    ev.currentTarget.style = "background-color: yellow";
    ev.preventDefault();
}

function drag(ev) {
    //alert("onzin");
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    var data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
    ev.preventDefault();
}



