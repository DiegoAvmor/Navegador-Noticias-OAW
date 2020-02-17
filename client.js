const MONTHS = [ { "name": "January" }, { "name": "February" }, { "name": "March" }, { "name": "April" }, { "name": "May" }, { "name": "June" }, { "name": "July" }, { "name": "August" }, { "name": "September" }, { "name": "October" }, { "name": "November" }, { "name": "December" } ];

getRSS("retrieved");//Obtiene las noticias una vez que se carga

//Realiza la accion de buscar las noticias en el url introducido
$('#btn').click(
    function(){
        let input = $('#searchInput');
        if(input){
            getRSS("search",input.val());
            input.val('');//Se limpia la busqueda
        }
    }
); 

/**
 * Metodo que tiene como funcion la obtencion de las noticias
 * a partir de la accion y url introducidos
 * @param {String} action La accion a realizar
 * @param {String} url la url con la que se efectuara la accion
 */
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

/**
 * Metodo cuya funcion es la visualizacion de las noticas obtenidas, creacion
 * de sublistas y asignacion de noticias a su categoria correspondiente
 * @param {Array} feed Arreglo de noticias a visualizar
 * @param {Object} domList El Objeto de documento donde se añadira la noticia
 * @param {String} action La accion con el cual se añadira las noticas a las listas correspondientes
 */
function setInformation(feed,domList,action){
    
    domList.empty();//Eliminacion de la busqueda anterior
    feed.forEach(element => {
        let dateSelection = dateToCompare(action,element.Date);
        console.log(dateSelection);

        let feedContent = document.createElement('div');
        feedContent.setAttribute('class','list-group-item');

        let tittle = document.createElement('a'); //Creacion del encabezado
        tittle.innerHTML = element.Tittle + " Date: " + element.Date;
        tittle.setAttribute('href',element.TittleURL); //Se adiciona el url de la noticia al encabezado
        feedContent.appendChild(tittle);

        let descriptionContent = document.createElement('div');//Creacion del cuerpo de la noticia
        let author = document.createElement('p');
        author.setAttribute('class','text-justify');
        author.innerHTML = element.Author;
        let description = document.createElement('p');
        description.setAttribute('class','text-justify');
        description.innerHTML = element.Description;

        descriptionContent.appendChild(author);
        descriptionContent.appendChild(description);
        feedContent.appendChild(descriptionContent);

        if(checkIfExists(dateSelection)){//Verifica si una sublista existe y en base a eso hace lo correspondiente
            let sublist = $('div#'+dateSelection);
            sublist.append(feedContent);
        }else{
            createNewList(dateSelection,domList);
            let sublist = $('div#'+dateSelection);
            sublist.append(feedContent);
        }

    });
}

/**
 * 
 * @param {String} listname 
 * @param {Object} list 
 */
function createNewList(listname,list){
    let collapsableArrow = document.createElement('a');
    collapsableArrow.setAttribute('href','#'+listname);
    collapsableArrow.setAttribute('data-toggle',"collapse");
    collapsableArrow.setAttribute('class','list-group-item');
    let collapsableTittle = document.createElement('i');
    collapsableTittle.setAttribute('class','glyphicon glyphicon-chevron-right');

    //Encabezado de la lista colapsable
    collapsableArrow.append(collapsableTittle);
    collapsableArrow.append(listname);

    list.append(collapsableArrow);

    //Se crea el apartado para el contenido
    let collapsableContent = document.createElement('div');
    collapsableContent.setAttribute('class','list-group collapse');
    collapsableContent.setAttribute('id',listname);

    list.append(collapsableContent);
}


function sortByDate(array){
    var domList;
    let orderedByYear = array.slice().sort(function(a,b){
        let firstYear = new Date(a.Date).getFullYear();
        let secondYear = new Date(b.Date).getFullYear();
        return firstYear < secondYear ? -1 : firstYear > secondYear ? 1 : 0
    });
    domList = $('div#order-year');
    setInformation(orderedByYear,domList,"Year");
    
    let orderedByMonth = array.slice().sort(function(a,b){
        let firstMonth = new Date(a.Date).getMonth();
        let secondMonth = new Date(b.Date).getMonth();
        return firstMonth < secondMonth ? -1 : firstMonth > secondMonth ? 1 : 0
    });
    domList = $('div#order-month');
    setInformation(orderedByMonth,domList,"Month");


    let orderedByDay = array.slice().sort(function(a,b){
        let firstDay = new Date(a.Date).getDate();
        let secondDay = new Date(b.Date).getDate();
        return firstDay < secondDay ? -1 : firstDay > secondDay ? 1 : 0
    });
    domList = $('div#order-day');
    setInformation(orderedByDay,domList,"Day");

}


//Realiza el colapso de las sublistas
$(function() {
    $('.list-group-item').on('click', function() {
      $('.glyphicon', this)
        .toggleClass('glyphicon-chevron-right')
        .toggleClass('glyphicon-chevron-down');
    });
  
});

/**
 * Metodo que verifica si existe la estructura indicado en el DOM
 * @param {Object} item El objeto a verificar la existencia
 */
function checkIfExists(item){
    if($('div#' + item).length){
        return true;
    }else{
        return false;
    }
}

/**
 * Metodo cuya funcion es obtener el tipo de fecha correspondiente a 
 * la accion que se pide.
 * @param {String} action La accion con la que obtendra el tipo de fecha correspondiente
 * @param {String} dateValue La fecha de la noticia
 */
function dateToCompare(action,dateValue){
    let selectedDate = new Date(dateValue);
    if(action === "Year"){
        return selectedDate.getUTCFullYear();
    }
    if(action === "Month"){
        return MONTHS[selectedDate.getUTCMonth()].name;
    }
    if(action === "Day"){
        return selectedDate.getUTCDate();
    }
}