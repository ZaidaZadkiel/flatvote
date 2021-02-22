import TimeAgo from 'javascript-time-ago'
// English.
import en from 'javascript-time-ago/locale/en'
TimeAgo.addDefaultLocale(en)
// const timeAgo =
var TimeAgoFormatter = new TimeAgo('en-US');
var serverTimeInternal;

export function timeAgo(oldTime){
  console.log("timeAgo",serverTimeInternal);
  if(!oldTime || isNaN(oldTime) ) return "...";
  if(serverTimeInternal) return TimeAgoFormatter.format(oldTime, {now: serverTimeInternal});
  return oldTime

};

export function createUser(userobj){
  console.table(userobj);
  return fetch(getUrl("createUser.php"),
    {
      method: 'POST', // or 'PUT'
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ "username":FormData.username,
                             "password":FormData.password
                            }),
    }
  )
  .then(res  => updateServerDate(res))
  .then(json => json);
}

export function castVote (vote){
    console.table(FormData);

    return fetch(getUrl("castVote.php"),
      {
        method: 'POST', // or 'PUT'
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ "id_question":FormData.id_question,
                               "id_user"    :FormData.id_user,
                               "id_ballot"  :FormData.id_ballot,
                               "txt_comment":FormData.txt_comment
                              }),
      }
    )
    .then(res  => updateServerDate(res))
    .then(json => json);
    // .then(json => {console.table(json); setVoteRes(json); if(json.count) setCount(json.count) });
  }


  export function getUrl (path){
    console.log("http://zaidazadkiel.com/flatvote/db/"+path);
    return process.env.NODE_ENV == "development"
      ? "http://zaidazadkiel.com/flatvote/db/"+path  //"https://cors-anywhere.herokuapp.com/"
      : "http://localhost:8888/"+path  

  }
  // function castVote(){getQuestion()}

  export function updateServerDate (res){
    var x;
    for (var pair of res.headers.entries()) {
       console.log(pair[0]+ ': '+ pair[1]);
       if(pair[0] =="timestamp") x = pair[1];
    }
    if(x) {
      console.log("now date", x)
      serverTimeInternal=new Date(x);
      // setServerDate(new Date(x));
    }
    return res.json();
  }

  export function getDocument(id){
    return fetch(getUrl(`getDocument.php?id=${id}`))
    .then(res => updateServerDate(res))
    .then(json => json);
    //   {
    //   console.log(json);
    //   setDocument(json);
    // });
  }

  export function getAllUsers(){
      return fetch(getUrl(`getUsers.php`))
      .then(res => updateServerDate(res))
      .then(json => {
        return json;
        // console.log(json);
        // setUsers(json);
      });
    }

  export function getQuestion (question){
    return fetch(getUrl(`getQuestion.php?id=${question}`))
    .then(res => updateServerDate(res))
    .then(json => {return json;
      // console.log(json);
      // setObjQuestion(json);
      // setCount(json.count);
      // setFormData({...FormData, id_question: json.id_question})
    });
  }

  export function listQuestions (){
    return fetch(getUrl("listQuestions.php"))
    .then(res => updateServerDate(res))
    .then(json => {
      return json;
      // console.log(json);
      // console.log(json[1].ts_date);
      // setLstQuestions(json);
    });
  }
  // function castVote(vote){
  //   console.table(FormData);
  //
  //   fetch(debugUrl()+getUrl("castVote.php"),
  //     {
  //       method: 'POST', // or 'PUT'
  //       headers: {
  //         'Content-Type': 'application/json',
  //       },
  //       body: JSON.stringify({ "id_question":FormData.id_question,
  //                              "id_user"    :FormData.id_user,
  //                              "id_ballot"  :FormData.id_ballot,
  //                              "txt_comment":FormData.txt_comment
  //                             }),
  //     }
  //   )
  //   .then(res  => updateServerDate(res))
  //   .then(json => {console.table(json); setVoteRes(json); if(json.count) setCount(json.count) });
  // }
  // export {castVote, getDocument, getQuestion, getAllUsers, timeAgo, listQuestions};
