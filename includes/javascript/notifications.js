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
  }

}
