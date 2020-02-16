getURL();//Obtiene las noticias una vez que se carga

$('#btn').click(
    function(){
        let input = $('#searchInput').val();
        if(input){
            getRSS("search",input);
        }
    }
); 

function getURL(){
    getRSS("retrieved");
}



function getRSS(action,url="none"){
    $.ajax({
        url: "server.php",
        type: "get", //send it through get method
        data: { 
          url: url,
          action: action
        },
        success: function(response) {
            let parsedResponse = JSON.parse(response);
            let responseArray = new Array();
            parsedResponse.forEach(element => {
                let elementParsed = JSON.parse(element);
                responseArray.push(elementParsed);
            });
            sortByDate(responseArray);
        },
        error: function(xhr) {
          console.log("Error");
        }
      });
}

function setInformation(feed,domList){
    
    domList.empty();
    feed.forEach(element => {
        let feedContent = document.createElement('div');
        feedContent.setAttribute('class','list-group-item');

        let tittle = document.createElement('a'); //Creacion del encabezado
        tittle.innerHTML = element.Tittle + " Date: " + element.Date;
        tittle.setAttribute('href',element.TittleURL); //Se adiciona el url de la noticia al encabezado
        feedContent.appendChild(tittle);

        let descriptionContent = document.createElement('div');
        let author = document.createElement('p');
        author.setAttribute('class','text-justify');
        author.innerHTML = element.Author;
        let description = document.createElement('p');
        description.setAttribute('class','text-justify');
        description.innerHTML = element.Description;

        descriptionContent.appendChild(author);
        descriptionContent.appendChild(description);
        feedContent.appendChild(descriptionContent);

        domList.append(feedContent);

    });
}


function sortByDate(array){
    var domList;
    let orderedByYear = array.slice().sort(function(a,b){
        let firstYear = new Date(a.Date).getFullYear();
        let secondYear = new Date(b.Date).getFullYear();
        return firstYear < secondYear ? -1 : firstYear > secondYear ? 1 : 0
    });
    domList = $('div#order-year');
    setInformation(orderedByYear,domList);
    
    let orderedByMonth = array.slice().sort(function(a,b){
        let firstMonth = new Date(a.Date).getMonth();
        let secondMonth = new Date(b.Date).getMonth();
        return firstMonth < secondMonth ? -1 : firstMonth > secondMonth ? 1 : 0
    });
    domList = $('div#order-month');
    setInformation(orderedByMonth,domList);


    let orderedByDay = array.slice().sort(function(a,b){
        let firstDay = new Date(a.Date).getDate();
        let secondDay = new Date(b.Date).getDate();
        return firstDay < secondDay ? -1 : firstDay > secondDay ? 1 : 0
    });
    domList = $('div#order-day');
    setInformation(orderedByDay,domList);

}


//Collapse items
$(function() {
        
    $('.list-group-item').on('click', function() {
      $('.glyphicon', this)
        .toggleClass('glyphicon-chevron-right')
        .toggleClass('glyphicon-chevron-down');
    });
  
  });