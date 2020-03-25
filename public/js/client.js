const MONTHS = [
  { name: "January" },
  { name: "February" },
  { name: "March" },
  { name: "April" },
  { name: "May" },
  { name: "June" },
  { name: "July" },
  { name: "August" },
  { name: "September" },
  { name: "October" },
  { name: "November" },
  { name: "December" }
];


// Realiza la acción de buscar las noticias en el url introducido
$("#register-button").click(function() {
  let input = $("#register-input");
  if (input.val()) {
    $.ajax({
      type: "GET",
      // Cargar noticias de URL de archivo de configuración.
      url: "../../Navegador-Noticias-OAW/src/register_website.php",
      // Provided RSS URL
      data: {
        url: input.val()
      }
    }).fail((xhr, status, error) => console.log(error));

    input.val(""); //Se limpia la búsqueda
  }
});
$("#matchBtn").click(function(){
  let input = $("#matchInput");
  if (input.val()) {
    $.ajax({

      type:"GET",
      url:"../../Navegador-Noticias-OAW/src/search.php",
      data:{
        word:$("#matchInput").val()
      }
    })
    .done(sortAndShow)
    .fail((xhr,status,error)=> console.log(error));
    input.val("");
  }
});

const sortAndShow = response => {
  console.log(response);
  const parsedResponse = JSON.parse(response);
  let responseArray = new Array();
  parsedResponse.forEach(element => {
    const elementParsed = JSON.parse(element);
    responseArray.push(elementParsed);
  });
  sortByDate(responseArray);
};

//Realiza el colapso de las sublistas
$(function() {
  $(".list-group-item").on("click", function() {
    $(".glyphicon", this)
      .toggleClass("glyphicon-chevron-right")
      .toggleClass("glyphicon-chevron-down");
  });
});

/**
 * Método cuya función es la visualización de las noticias obtenidas, creación
 * de sub listas y asignación de noticias a su categoría correspondiente
 * @param {Array} feed Arreglo de noticias a visualizar
 * @param {Object} domList El Objeto de documento donde se añadirá la noticia
 * @param {String} action La acción con el cual se añadirá las noticias a las listas correspondientes
 */
function setInformation(feed, domList, action) {
  domList.empty(); // Eliminación de la búsqueda anterior
  feed.forEach(element => {
    let dateSelection = dateToCompare(action, element.last_modified);

    let feedContent = document.createElement("div");
    feedContent.setAttribute("class", "list-group-item");

    let title = document.createElement("a"); // Creación del encabezado
    title.innerHTML = element.title + " Date: " + element.last_modified;
    title.setAttribute("href", element.url); // Se adiciona el url de la noticia al encabezado
    feedContent.appendChild(title);

    let descriptionContent = document.createElement("div"); // Creación del cuerpo de la noticia
    let description = document.createElement("p");
    description.setAttribute("class", "text-justify");
    description.innerHTML = element.description;
    let keywords = document.createElement("p");
    keywords.setAttribute("class", "text-justify");
    keywords.textContent = "["+element.keywords +"]";

    descriptionContent.appendChild(description);
    descriptionContent.appendChild(keywords);
    feedContent.appendChild(descriptionContent);

    if (checkIfExists(dateSelection)) {
      // Verifica si una sub lista existe y en base a eso hace lo correspondiente
      let subList = $("div#" + dateSelection);
      subList.append(feedContent);
    } else {
      createNewList(dateSelection, domList);
      let subList = $("div#" + dateSelection);
      subList.append(feedContent);
    }
  });
}

/**
 * Método encargado de la creación de nuevas sub listas en la lista
 * padre
 * @param {String} listName Nombre de la nueva lista
 * @param {Object} list Lista padre al cual se le añadirá la nueva sub lista
 */
function createNewList(listName, list) {
  let collapsableArrow = document.createElement("a");
  collapsableArrow.setAttribute("href", "#" + listName);
  collapsableArrow.setAttribute("data-toggle", "collapse");
  collapsableArrow.setAttribute("class", "list-group-item");
  let collapsableTitle = document.createElement("i");
  collapsableTitle.setAttribute("class", "glyphicon glyphicon-chevron-right");

  // Encabezado de la lista plegable
  collapsableArrow.append(collapsableTitle);
  collapsableArrow.append(listName);

  list.append(collapsableArrow);

  // Se crea el apartado para el contenido
  let collapsableContent = document.createElement("div");
  collapsableContent.setAttribute("class", "list-group collapse");
  collapsableContent.setAttribute("id", listName);

  list.append(collapsableContent);
}

/**
 * Método que realiza el ordenamiento del arreglo de noticias en base
 * a año,mes y dia
 * @param {Array} array Arreglo de noticias con el cual se realizara el orden correspondiente
 */
function sortByDate(array) {
  var domList;
  let orderedArray = array.slice().sort(function(a, b) {
    return new Date(a.last_modified) - new Date(b.last_modified);
  });
  domList = $("div#order-year");
  setInformation(orderedArray, domList, "Year");

  domList = $("div#order-month");
  setInformation(orderedArray, domList, "Month");

  domList = $("div#order-day");
  setInformation(orderedArray, domList, "Day");
}

// Realiza el colapso de las sub listas
$(function() {
  $(".list-group-item").on("click", function() {
    $(".glyphicon", this)
      .toggleClass("glyphicon-chevron-right")
      .toggleClass("glyphicon-chevron-down");
  });
});

/**
 * Método que verifica si existe la estructura indicado en el DOM
 * @param {String} item El nombre del objeto de tipo div a verificar
 */
function checkIfExists(item) {
  if ($("div#" + item).length) {
    return true;
  } else {
    return false;
  }
}

/**
 * Método cuya función es obtener el tipo de fecha correspondiente a
 * la acción que se pide.
 * @param {String} action La acción con la que obtendrá el tipo de fecha correspondiente
 * @param {String} dateValue La fecha de la noticia
 */
function dateToCompare(action, dateValue) {
  let selectedDate = new Date(dateValue);
  if (action === "Year") {
    return selectedDate.getUTCFullYear();
  }
  if (action === "Month") {
    return MONTHS[selectedDate.getUTCMonth()].name;
  }
  if (action === "Day") {
    return selectedDate.getUTCDate();
  }
}
