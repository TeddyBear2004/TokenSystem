function onSubmit() {
    const token = document.getElementById("token").value;
    const name = document.getElementById("name").value;
    const result = document.getElementById("result");

    if (token === "" || name === "") {
        result.innerText = "Fülle zuerst alles aus!";
        return;
    }
    if (!(/^[a-zA-Z0-9_]+$/.test(name))) {
        result.innerText = "Der Minecraft Name enthält falsche Zeichen!";
        return;
    }

    let xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            result.innerHTML = xmlhttp.responseText;
            document.getElementById("token").value = "";
            document.getElementById("name").value = "";
        }
    }

    xmlhttp.open("GET", "./php/processAction.php?name=" + name + "&token=" + token /*+ "&key=" + key*/);
    xmlhttp.send();
}

//document.getElementById("submit").addEventListener("click", onSubmit, true);
