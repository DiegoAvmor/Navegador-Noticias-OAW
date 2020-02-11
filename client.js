const newsContainer = document.getElementById('rssContainer');
const searchBTN = document.getElementById('btn');
const textInput = document.getElementById('searchInput');

_getURL();

searchBTN.onclick = function(){
    let input = textInput.value;
    if(input && validURL(input)){
        getRSS(input,"search");
    }
}

function _getURL(){
    getRSS("none","retrieved");
}



function getRSS(url,action){
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.status == 200 && this.readyState == 4) {
            let parsedResponse = JSON.parse(this.response);
                setInformation(parsedResponse);
        }
        
    }
    xmlhttp.open("get", "server.php?url="+url+"&action="+action, true);
    xmlhttp.send();
}

function setInformation(feed){
    
    while(newsContainer.hasChildNodes()){//Borra la busqueda anterior
        newsContainer.removeChild(newsContainer.firstChild);
    }
    
    feed.forEach(element => {
        let json  = JSON.parse(element);
        console.log(json);
        let elementContainer = document.createElement('div');

        let tittle = document.createElement('h1');
        tittle.innerText = json.Tittle;
        let url = document.createElement('a');
        url.href = json.TittleURL;
        url.innerHTML = "Link";
        let author = document.createElement('h3');
        author.innerText = json.Author;
        let date = document.createElement('h5');
        date.innerText = json.Date;
        let description = document.createElement('p');
        description.innerHTML = json.Description;

        elementContainer.appendChild(tittle);
        elementContainer.appendChild(url);
        elementContainer.appendChild(author);
        elementContainer.appendChild(date);
        elementContainer.appendChild(description);

        newsContainer.appendChild(elementContainer);

    });
}

function validURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
      '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
  }