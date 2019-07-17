var x = document.getElementsByClassName("changeable");

for (var i = 0; i < x.length; i++) {
    x[i].style.display = "none";
}

var show = document.getElementById("show").innerHTML;

document.getElementById(show).style.display = "block";
document.getElementsByName(show)[0].classList.add("active");

document.getElementById("image-upload").style.height = $(window).height()-85 + "px";
document.getElementById("image-upload").style.maxHeight = $(window).height()-85 + "px";
document.getElementById("image-load").style.width = $(window).width()-document.getElementById("image-upload").style.width-3 + "px";
document.getElementById("image-load").style.maxWidth = document.getElementById("image-upload").style.width-3 + "px";

$(".nav-text").click(function() {
    for (var i = 0; i < x.length; i++) {
        document.getElementsByClassName("changeable")[i].style.display = "none";
    }

    for (var i = 0; i < x.length; i++) {
        document.getElementsByClassName("nav-text")[i].classList.remove("active");
    }


    document.getElementById(this.getAttribute("name")).style.display = "block";
    this.classList.add("active");
});

function previewFile() {
    var preview = document.querySelector("#myImg");
    var file = document.querySelector("#image").files[0];

    var reader = new FileReader();
    reader.onloadend = function() {
        preview.src = reader.result;
    }

    if (file) {
        reader.readAsDataURL(file);
    }

    else {
        preview.src = "#";
    }
}

previewFile();

var ISO = document.getElementById("languages").innerHTML;
ISO = JSON.parse(ISO);

var languages = [];
for (var i = 0; i < ISO.length; i++) {
    languages[i] = (ISO[i]['English']);
}

if (document.getElementById("status") != "") {
    var request = new XMLHttpRequest();
    var url = document.getElementById("url").innerHTML + ":5000/objrec?image=" + document.getElementById("status").innerHTML;
    request.open("GET", url);
    request.onload = function() {
        $("#myImg").attr("src", document.getElementById("status").innerHTML);
        var labels = JSON.parse(this.response);

        var font = 'font-family: "Dosis", sans-serif; font-weight: 800; font-size: 40px;';
        document.getElementById("labels").innerHTML +=
            "<h1 class = 'heading' style = '" + font + "'>I SAW THESE OBJECTS IN THE PICTURE!</h1>";

        for (var i = 0; i < labels.length; i++) {
            document.getElementById("labels").innerHTML +=
                "<div class = 'display-text'>" + labels[i] + "</div>"
        }

        document.getElementById("translateText").innerHTML +=
            "<button class = 'display-text-inverted' id = 'add'>Add to My List</button>";
        document.getElementsByClassName('display-text')[0].classList.add("active");

        $('.display-text').click(function() {
           var divs = document.getElementsByClassName('display-text');
           for (var i = 0; i < divs.length; i++) {
               divs[i].classList.remove("active");
           }
           this.classList.add("active");
        });

        $('#add').click(function() {
            var language = document.getElementById("translate").innerHTML;
            var labels = document.getElementsByClassName("display-text");
            var textToTranslate = "";
            for (var i = 0; i < languages.length; i++) {
                if (language == ISO[i]["English"]) {
                    language = ISO[i]["alpha2"];
                    break;
                }
            }

            for (var i = 0; i < labels.length; i++) {
                if (labels[i].classList.contains("active")) {
                    textToTranslate = labels[i].innerHTML;
                }
            }

            var request2 = new XMLHttpRequest();
            var url = document.getElementById("url").innerHTML + ":5000/translateobj?q="
                + textToTranslate + "&lang=" + language;
            request2.open("GET", url);

            request2.onload = function() {
                var translated = this.response;
                var url = document.getElementById("status").innerHTML;
                var text = textToTranslate;
                var lang = document.getElementById("translate").innerHTML;
                var langcode = language;
                $.ajax({
                    url: 'upload.php',
                    type: 'POST',
                    data: {url: url, text: text, translated: translated, lang: lang, langcode: langcode},
                    error: function(request, error) {
                        alert("Yo!");
                        console.log("Error: " + error);
                    },
                    success: function(response) {
                        console.log("Response: " + response);
                        window.location.reload();
                    }
                });
            }

            request2.send();
        });
    }

    request.send();
}

function autocomplete(inp, arr) {
    var currentFocus;
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        this.parentNode.appendChild(a);
        for (i = 0; i < arr.length; i++) {
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                b = document.createElement("DIV");
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                b.addEventListener("click", function(e) {
                    inp.value = this.getElementsByTagName("input")[0].value;
                    closeAllLists();
                });
                a.appendChild(b);
            }
        }
    });

    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) {
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

autocomplete(document.getElementById("language"), languages);

function change(page) {
    for (var i = 0; i < document.getElementsByClassName("nav-text").length; i++) {
        document.getElementsByClassName("nav-text")[i].classList.remove("active");
    }
    document.getElementsByName(page)[0].classList.add("active");

    for (var i = 0; i < document.getElementsByClassName("changeable").length; i++) {
        document.getElementsByClassName("changeable")[i].style.display = "none";
    }
    document.getElementById(page).style.display = "block";
}