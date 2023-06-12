console.log("start");


function get_host(){
  document.getElementById('host').innerHTML = window.location.origin;

  let token = localStorage.getItem("apitoken");
  if(token) update_token(token);
  console.log({token});
}

function escapeJSON(obj){ return JSON.stringify(obj).replace(/"/g, '&quot;'); }

function update_token(token){
  document.getElementById("token").innerHTML=token;
  console.log("setting token");
  localStorage.setItem("apitoken", token);
}

function show_data(x, path, data){
  // console.log("show_data", x, path, data);

  let vars = JSON.parse(data);
  console.log(vars);

  let values = {};
  Object.keys(vars).forEach( variable => {
    let element = document.getElementById(x+"select"+variable);
    if(!element){
      console.warn(x+"select"+variable+' was not found in DOM')
      return;
    }

    let value   = "";
    switch(element.type){
      case 'file':
        if(element.files && element.files[0]){
          value = {
            lastModified: element.files[0].lastModified,
            name        : element.files[0].name,
            path        : element.files[0].path,
            size        : element.files[0].size,
            type        : element.files[0].type,
          }; // ???
        }
        break;
      default    : value = element.value;         break;
    }
    values[variable] = value;
  });

  // console.log("values", values);
  let token   = document.getElementById("token").textContent;
  let request = {
    method: "post",
    headers:(token?{"Authorization": token}:{}),
    body: JSON.stringify( values )
  }

  console.log("trying", path, request);

  fetch(x+"?action="+path, request)
  .then(x=>x.text())
  .then( res=>{
    console.log(res);
    let elementName = x+"select"+path;

    try{
      let json = JSON.parse(res);

      if(json.token) {
         // document.getElementById("token").innerHTML = json.token;
         update_token(json.token);
      }

      if(json.error) {
        return document.getElementById(elementName).innerHTML = `
          Response:<br/>
          ${json.error}
          ${Object.keys(values)
              .map(k =>
                `<div class="w3-codespan">
                <span class="${values[k]?"":"w3-white w3-text-red"}" >
                  ${k}(<i><small>${vars[k]}</small></i>):&nbsp;<b>${values[k]||"unset"}</b>
                </span>
                </div>`)
              .join("")}
        `;
      }

      if(json.upload_url){
        document.getElementById(elementName).innerHTML=`
          File upload first response:<br/>
          <pre
            id="${x}-file_upload_result"
            style="
              width: 80vw;
              word-break: break-word;
              white-space: pre-wrap;
            ">${JSON.stringify(json, null, 2)}</pre>`;

        let element  = document.getElementById(x+"selectfile");
        console.log(element);
        // let formData = new FormData();
        // formData.append("file", element.files[0], element.files[0].name);

        let upload = {
          method : "put",
          body   : element.files[0],
          headers: {
            "Authorization": token ? token : "",
            "apikey":        json.key,
            'Content-Type':  'multipart/form-data',
            // 'Content-Length': element.files[0].size
          }
        };

        fetch(
          json.upload_url,
          upload
        )
        .then(x=>x.text())
        .then(data=>{
          console.log(data);
          document.getElementById(elementName).innerHTML += `
            <div>
              File upload second section:<br/>
              ${data}
            </div>
          `;
        })
      } else {
        document.getElementById(elementName).innerHTML=`
          all right!<br/>
          <pre style="
            width: 80vw;
            word-break: break-word;
            white-space: pre-wrap;
          ">${JSON.stringify(json, null, 2)}</pre>`;
      }
    }catch (e) {
      console.error(e);
      document.getElementById(elementName).innerHTML=e.toString()+"<br/>"+res;
    }
  });
}

/* https://stackoverflow.com/a/20732091 */
function humanFileSize(size) {
    var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}

function setFileButtonText(event, id, scope){
  console.log(event);
  console.log(scope.files[0])
  document.getElementById(id).textContent=`${scope.files[0].name} (${humanFileSize(scope.files[0].size)})`;
}

// TODO: make the request based on the new format

function changeResult(id, section, str_params){
  let value   = document.getElementById(id).value;
  let actions = JSON.parse(str_params)[value];
  let escaped = escapeJSON(actions);
  console.log(id, section, str_params, value );

  const input = (k) => {
    if(k === 'is_public' ) return;

    if( Array.isArray(actions[k]) ){
      return (
        `<tr>
          <td><span>${ k }</span></td>
          <td>
            <select class="w3-dropdown-click w3-border-white w3-light-blue w3-round-large w3-block " id="${id+k}">
              ${actions[k].map(actions => `<option  value=${actions}>${actions}</option>`)}
            </select>
          </td>
        </tr>`
      );
    }

    if(actions[k].toLowerCase() === "file"){
      // <input class="w3-input" type="text"  id="${id+k}" placeholder="${actions[k]}" />
      return (
        `<tr>
          <td><span>${ actions[k].startsWith("optional") ? `<i>${k} (optional)</i>` : k}</span></td>
          <td>
            <input
              id="${id+k}"
              type="file"
              name="file" id="file"
              hidden
              onchange="setFileButtonText(event, '${id+k}-btn', this)"
            >
            <label for="file">
              <button
                id="${id+k}-btn"
                class="w3-btn w3-border-white w3-light-blue w3-round-large w3-block "
                onclick="document.getElementById('${id+k}').click()"
              >Select File</button>
            </label>
          </td>
        </tr>`
      );
    }

    return (
      `<tr>
        <td><span>${ actions[k].startsWith("optional") ? `<i>${k} (optional)</i>` : k}</span></td>
        <td>
          <input class="w3-input" type="text"  id="${id+k}" placeholder="${actions[k]}" />
        </td>
      </tr>`
    );
  };

  const actionkeys = Object.keys(actions);
  document.getElementById(section).style.display="block";
  document.getElementById(section).innerHTML=`
    ${actionkeys.length <= 1 && actionkeys[0] == 'is_public'
      ? `<tr><td>There are no parameters</td></tr>`
      : actionkeys
        .sort(
          (x,m)=>{
            let xt = (typeof actions[x] === "string") && actions[x].startsWith("optional");
            let mt = (typeof actions[m] === "string") && actions[m].startsWith("optional");
            return xt-mt;
          })
        .map(k=>input(k))
        .join("")}
    <tr>
      <td></td>
      <td >
        <button class="w3-block w3-button  w3-blue w3-round-large" onclick="show_data('${section}','${value}', '${escaped}')">send</button>
      </td>
    </tr>
    <tr ><td colspan="2" id="${id+value}" style="word-break:break-word;"></td></tr>
  `;
}

function show_file(x){
  fetch(x)
  .then(x=>x.text())
  .then(res=>{
    console.log(res);
    let data = JSON.parse(res);
    if(data.token) update_token(data.token);

    if(data.action){
      let selectid = x+"select";
      let escaped = escapeJSON(data.action);
      return document.getElementById(x+"-data").innerHTML=(
        `
          <label  for="${selectid}"> action: </label>
          <select
              id="${selectid}"
              class="w3-dropdown-click w3-border-white w3-light-blue w3-round-large"
              onclick="changeResult('${selectid}', '${x}', '${escaped}')">
            ${
              Object
              .keys(data.action)
              .map(actions => 
                `<option value=${actions}>${actions}${
                  data.action[actions].is_public 
                    ? " (public)" 
                    : ''
                  }</option>`)}
          </select>
        `
      );//return document.getElementById(x).innerHTML
    }
    return document.getElementById(x+"-data").innerHTML=JSON.stringify(data);
  });
}
