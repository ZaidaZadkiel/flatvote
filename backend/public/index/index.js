console.log("start");

function escapeJSON(obj){ return JSON.stringify(obj).replace(/"/g, '&quot;'); }

function update_token(token){
  document.getElementById("token").innerHTML=token;
}

function show_data(x, path, data){
  // console.log("show_data", x, path, data);

  let vars = JSON.parse(data);
  // console.log(vars);

  let values = {};
  Object.keys(vars).forEach( variable => {
    // console.log(x+variable);
    values[variable] = document.getElementById(x+"select"+variable).value
  });
  // console.log("values", values);
  let token = document.getElementById("token").textContent;
  let request = {method: "post", headers:(token?{"Authorization": token}:{}), body: JSON.stringify( values) }
  console.log("trying", path, request);

  fetch(x+"?action="+path, request).then(x=>x.text())
    .then( res=>{
      console.log(res);
      let elementName = x+"select"+path;
      try{
        let json = JSON.parse(res);
        if(json.token) {
           document.getElementById("token").innerHTML = json.token;
        }

        if(json.error) {
          return document.getElementById(elementName).innerHTML = `
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
        document.getElementById(elementName).innerHTML=`
          all right!<br/>
          <pre>${JSON.stringify(json, null, 2)}</pre>`;
      }catch (e) {
        document.getElementById(elementName).innerHTML=res;
      }
    });
}


// TODO: make the request based on the new format

function changeResult(id, section, str_params){
  let value = document.getElementById(id).value;
  console.log(id, section, str_params, value );
  let actions = JSON.parse(str_params)[value];
  let escaped = escapeJSON(actions);

  const input = (k) => (
    Array.isArray(actions[k])
      ?
        `<tr>
          <td><span>${ k }</span></td>
          <td>
            <select class="w3-dropdown-click w3-border-white w3-light-blue w3-round-large w3-block " id="${id+k}">
              ${actions[k].map(actions => `<option  value=${actions}>${actions}</option>`)}
            </select>
          </td>
        </tr>`
      :
        `<tr>
          <td><span>${ actions[k].startsWith("optional") ? `<i>${k} (optional)</i>` : k}</span></td>
          <td><input class="w3-input" type="text"  id="${id+k}" placeholder="${actions[k]}" /></td>
        </tr>`
  );
  document.getElementById(section).style.display="block";
  document.getElementById(section).innerHTML=`
    ${Object.keys(actions).map(k=>input(k)).join("")}
    <tr>
      <td></td>
      <td >
        <button class="w3-block w3-button  w3-blue w3-round-large" onclick="show_data('${section}','${value}', '${escaped}')">send</button>
      </td>
    </tr>
    <tr ><td colspan="2" id="${id+value}"></td></tr>
    `;
  }

function show_file(x){
  fetch(x).then(x=>x.text()).then(res=>{
      console.log(res);
      let data = JSON.parse(res);

      if(data.action){
        let selectid = x+"select";
        let escaped = escapeJSON(data.action);
        return document.getElementById(x+"-data").innerHTML=(
          `
            <label  for="${selectid}"> action: </label>
            <select id="${selectid}" class="w3-dropdown-click w3-border-white w3-light-blue w3-round-large" onclick="changeResult('${selectid}', '${x}', '${escaped}')">
              ${Object.keys(data.action).map(actions => `<option value=${actions}>${actions}</option>`)}
            </select>
          `
        );//return document.getElementById(x).innerHTML
      }
      return document.getElementById(x+"-data").innerHTML=JSON.stringify(data);
    });
}
