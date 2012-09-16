// ==UserScript==
// @id             edu.telematik.examfilter
// @name           Examfilter Telematik
// @version        1.0
// @namespace      
// @author         Tommy Sparber
// @description    Filter exams based und lv-number (data stored in HTML5 localstorage)
// @include        https://telematik.edu/Pr%C3%BCfungstermine
// @include        https://www.telematik.edu/Pr%C3%BCfungstermine
// @include        http://telematik.edu/Pr%C3%BCfungstermine
// @include        http://telematik.edu/Pr%C3%BCfungstermine
// @run-at         document-end
// ==/UserScript==

var lv_regex = /^([0-9]{3}[\.]{1}[0-9]{3}) [0-9]{2}[SW]{1} [0-9]{1}SSt (.*)$/

var table = document.getElementsByTagName("table")[0]
var rows = table.getElementsByTagName("tr")
var show = false
var test = []

init()

function init()
{
  test = JSON.parse(localStorage.getItem('examfilter'))
  if(test == null)
  {
    test = []
  }

  var form = document.createElement('form')
  form.setAttribute('action', '')
  var select = document.createElement('select')
  select.setAttribute('id', 'filter')
  form.appendChild(select)

  var rem_filter = document.createElement('input')
  rem_filter.setAttribute('type', 'submit')
  rem_filter.setAttribute('value', 'Filter löschen')

  form.onsubmit = function() { remFilter(); return false; }
  form.appendChild(rem_filter)

  var show_filter = document.createElement('input')
  show_filter.setAttribute('type', 'button')
  show_filter.setAttribute('value', 'Filter icon umschalten')
  show_filter.onclick = function() { show ? show = false : show = true;
                                     filterTable(); }
  form.appendChild(show_filter)

  table.parentNode.insertBefore(form, table.nextSibling)
  updateFilterList()
  filterTable()
}

function updateFilterList()
{
  var select = document.getElementById('filter')
  var cindex = select.selectedIndex
  
  while (select.hasChildNodes())
  {
    select.removeChild(select.lastChild);
  }
  
  var option = document.createElement('option')
  option.innerHTML = '-'
  select.appendChild(option)

  for(var i = 0; i < test.length; i++)
  {
    var option = document.createElement('option')
    test[i].match(lv_regex)
    option.innerHTML = RegExp.$1 + " " + RegExp.$2
    select.appendChild(option)
  }
  
  if(cindex >= select.length)
  {
   cindex--
  }
  
  if(cindex > -1)
  {
    select.selectedIndex = cindex;
  }
  
  
}

function filterThis(lv)
{
  test.push(lv)
  localStorage.setItem('examfilter', JSON.stringify(test))
  updateFilterList()
  filterTable()
}

function remFilter()
{
  var select = document.getElementById('filter') 
  
  if(select.selectedIndex == 0)
  {
    return
  }
    
  var to_rem = select.options[select.selectedIndex].value
  
  for(var i = 0; i < test.length; i++)
  {
    test[i].match(lv_regex)
    if(RegExp.$1 + " " + RegExp.$2 == to_rem)
    {
      test.splice(i, 1)
    }
  }
  
  localStorage.setItem('examfilter', JSON.stringify(test))
  updateFilterList()
  filterTable()
}

function showFilter(elem, lv)
{
  var rem = document.createElement('img')
  rem.setAttribute('src', 
   'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGd\
    BTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAHdSURB\
    VDjLpZNraxpBFIb3a0ggISmmNISWXmOboKihxpgUNGWNSpvaS6RpKL3Ry//Mh1wgf6PElaCyz\
    q67O09nVjdVlJbSDy8Lw77PmfecMwZg/I/GDw3DCo8HCkZl/RlgGA0e3Yfv7+DbAfLrW+SXOv\
    LTG+SHV/gPbuMZRnsyIDL/OASziMxkkKkUQTJJsLaGn8/iHz6nd+8mQv87Ahg2H9Th/BxZqxE\
    kEgSrq/iVCvLsDK9awtvfxb2zjD2ARID+lVVlbabTgWYTv1rFL5fBUtHbbeTJCb3EQ3ovCnRC\
    6xAgzJtOE+ztheYIEkqbFaS3vY2zuIj77AmtYYDusPy8/zuvunJkDKXM7tYWTiyGWFjAqeQnA\
    D6+7ueNx/FLpRGAru7mcoj5ebqzszil7DggeF/DX1nBN82rzPqrzbRayIsLhJqMPT2N83Sdy2\
    GApwFqRN7jFPL0tF+10cDd3MTZ2AjNUkGCoyO6y9cRxfQowFUbpufr1ct4ZoHg+Dg067zduTm\
    Ebq4yi/UkYidDe+kaTcP4ObJIajksPd/eyx3c+N2rvPbMDPbUFPZSLKzcGjKPrbJaDsu+dQO3\
    msfZzeGY2TCvKGYQhdSYeeJjUt21dIcjXQ7U7Kv599f4j/oF55W4g/2e3b8AAAAASUVORK5CY\
    II=')
  rem.setAttribute('class', 'filterButton')
  rem.style.cursor = "pointer"
  rem.onclick = function() { filterThis(lv); };
  elem.appendChild(rem)
}

function hideFilter(elem)
{
  if(elem.lastChild.attributes != null &&
     elem.lastChild.className == 'filterButton')
  {
  
    elem.removeChild(elem.lastChild)
  }
}

function filterTable()
{
  for(var row = 1; row < rows.length; row++)
  {
    var cells = rows[row].cells
    var lv = cells[0].getElementsByTagName("a")[0].innerHTML
    
    hideFilter(cells[0]);
    
    if(show)
    {
      showFilter(cells[0], lv);
    }
    
    lv.match(lv_regex);
    var lv_number = RegExp.$1;
    var lv_title = RegExp.$2;
    
    rows[row].style.display = 'table-row'
    
    for(var i = 0; i < test.length; i++)
    {
      test[i].match(lv_regex)
        
      if(lv_number == RegExp.$1)
      {
        rows[row].style.display = 'none'
        break
      }
    }
  }
}
