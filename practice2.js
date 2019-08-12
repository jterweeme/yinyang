/*
Jasper ter Weeme 2019
*/

var g_answers, g_finish, g_vfinish;

function allowDrop(ev)
{
    ev.currentTarget.style = "background-color: yellow";
    ev.preventDefault();
}

function drag(ev)
{
    ev.dataTransfer.setData("text", ev.target.id);
}

function getAnswerHandle(xid)
{
    for (var i = 0; i < g_answers.length; i++)
    {
        if (g_answers[i].name == xid)
            return g_answers[i];
    }

    return null;
}

function drop(ev)
{
    ev.preventDefault();    // anders gaat de browser naar div0.com :S
    var targetLi = parseInt(ev.target.id[2]);

    // de id wordt via dataTransfer verstuurd
    var xid = ev.dataTransfer.getData("text");

    var handle = getAnswerHandle(xid);
    handle.value = targetLi;
    console.log(handle);

    ev.target.appendChild(document.getElementById(xid));

}

function onfinish(ev)
{
    g_finish.disabled = !g_vfinish.checked;
}

function init()
{
    g_finish = document.getElementById("finish");
    g_vfinish = document.getElementById("vfinish");
    g_vfinish.addEventListener("click", onfinish, false);
    xtype = document.getElementById("xtype");
    
    if (xtype.value == "dragdrop")
    {
        g_answers = document.getElementsByClassName("answer");
        drops = document.getElementsByClassName("drop");
    
        for (var i = 0; i < drops.length; i++)
        {
            drops[i].addEventListener("drop", drop, false);
            drops[i].addEventListener("dragover", allowDrop, false);
        }
    
        drags = document.getElementsByClassName("drag");
    
        for (var i = 0; i < drags.length; i++)
        {
            drags[i].addEventListener("dragstart", drag, false);
            drags[i].draggable = true;
        }
    }
}

document.addEventListener("DOMContentLoaded", init, false);



