/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/>.
 */

function handler(){

  var count = 0;
  var messages = JSON.parse(this.responseText);

  for (var i = 0; i < messages.length; i++) {
    if(messages[i].status === 'unread') count++;
  }
  if(count > 0){
    var element = document.getElementById("notification");
    element.innerHTML = element.innerHTML + count;
    element.classList.add("new-notification");
  }

}


function getNotifications(url, session){
  // console.log('URL:', url);
  // console.log('UUID:', session.uuid);
  var xmlhttp = new XMLHttpRequest();
  if("withCredentials" in xmlhttp){
    xmlhttp.open("GET", url + '/' + session.uuid);
    xmlhttp.withCredentials = "true";
    xmlhttp.onload = handler;
    xmlhttp.send();
  }else{
    xmlhttp.open("GET", url + '/' + session.uuid);
    xmlhttp.withCredentials = "true";
    xmlhttp.onload = handler;
    xmlhttp.send();
  }

}

function getAnnounce(){
  var xmlhttp = new XMLHttpRequest();
  var url = 'http://172.17.0.1:3000/announce';
  if("withCredentials" in xmlhttp){
    xmlhttp.open("GET",  url);
    xmlhttp.withCredentials = "true";
    xmlhttp.addEventListener("error", handlerAnnouce, false);
    xmlhttp.onload = handlerAnnouce;
    xmlhttp.send();
  }else{
    xmlhttp.open("GET",  url);
    xmlhttp.withCredentials = "true";
    xmlhttp.onload = handlerAnnouce;
    xmlhttp.addEventListener("error", handlerAnnouce, false);
    xmlhttp.send();
  }

}

function handlerAnnouce(){

  var element = document.getElementById("announce");

  if(this.responseText) {
    try {
      var response = JSON.parse(this.responseText);
    } catch (e) {
      var response = {};
    }
    var data = {
      image: response.image || null,
      link: response.link || null,
      text: response.text || null
    }
    if(data.image && data.link){
      element.innerHTML = element.innerHTML + '\
      <a href="' + data.link + '" target=_blank alt="' + data.text + '">\
        <img width=600 height=400 align=center src="' + data.image + '">\
      </a>';
    }else{
        useDefaultAnnounce(element);
    }

  }else{
    useDefaultAnnounce(element);

  }

}

function useDefaultAnnounce(element){

  console.log('Using default announce');

  var server = window.location.host;
  element.innerHTML = element.innerHTML + '\
  <a href="http://' + server + ':8080" target=_blank>\
    <img width=600 height=400 align=center src="/snep/images/qmanager-banner.png">\
  </a>';

}
