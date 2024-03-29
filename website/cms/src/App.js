import React, { useState, useEffect, useRef } from 'react';
import ReactDOM     from "react-dom";
// import { register } from "codelift";
// import TimeAgo           from 'javascript-time-ago'
import {
  getDocument,
  getQuestion,
  getAllUsers,
  listQuestions,
  timeAgo
} from './fetches.jsx';
import ViewVote   from './ViewVote.jsx';
import CreateUser from './CreateUser.jsx';
import LoginUser  from './LoginUser.jsx';
import Header     from './pieces/Header'
import {useAuth}  from './authContext';



// register({ React, ReactDOM });

const version = require("./version.json");

function App() {
  const [auth, setAuth] = useAuth();

  const LoginLinks= (
      <div className="m-4">
        <div className="text-center">Need to login</div><br/>
        <span className="btn ">
          <button  onClick={()=>setMainView(<LoginUser/>)}>Login</button>
        </span>
        <span className="px-4">
          <button  onClick={()=>setMainView(<CreateUser/>)}>Sign up</button>
        </span>
      </div>
  );

  const Overview=(
    <div className="flex text-left">
      <div className="align-left">
        <h1>Overview</h1>
        <ul>
          <li><button onClick={()=>setMainView(ProjectView)  } >Projects</button></li>
          <li><button onClick={()=>setMainView(DocumentsView)} >Documents</button></li>
          <li><button onClick={()=>setMainView(VotesView)    } >Votes</button></li>
        </ul>
      </div>
      <div>
        <h1>text</h1>
      </div>
    </div>
  );

  const ProjectView = (
    <div>
      Projects
      <button className="px-4" onClick={()=>setMainView(Overview)}>Go to Overview</button>
    </div>
  );
  const DocumentsView = (
    <div>
      Documents
      <button className="px-4" onClick={()=>setMainView(Overview)}>Go to Overview</button>
    </div>
  );
  const VotesView = (
    <div>
      Votes
      <button className="px-4" onClick={()=>setMainView(Overview)}>Go to Overview</button>
    </div>
  );

  // function getUrl(path){console.log("http://zaidazadkiel.com/flatvote/db/"+path); return "http://zaidazadkiel.com/flatvote/db/"+path }
  // function debugUrl(path){return process.env.NODE_ENV == "development" ? "https://cors-anywhere.herokuapp.com/" : "" }
  // function castVote(){getQuestion()}
  //
  // function updateServerDate(res){
  //   var x;
  //   for (var pair of res.headers.entries()) {
  //      console.log(pair[0]+ ': '+ pair[1]);
  //      if(pair[0] =="timestamp") x = pair[1];
  //   }
  //   if(x) {
  //     console.log("now date", x)
  //     setServerDate(new Date(x));
  //   }
  //   return res.json();
  // }
  //
  // function getDocument(id){
  //   fetch(getUrl(`getDocument.php?id=${id}`))
  //   .then(res => updateServerDate(res))
  //   .then(json => {
  //     console.log(json);
  //     setDocument(json);
  //   });
  //   listQuestions();
  //
  //
  //   fetch(getUrl(`getUsers.php`))
  //   .then(res => updateServerDate(res))
  //   .then(json => {
  //     console.log(json);
  //     setUsers(json);
  //   });
  // }
  //
  // function getQuestion(question){
  //   fetch(getUrl(`getQuestion.php?id=${question}`))
  //   .then(res => updateServerDate(res))
  //   .then(json => {
  //     console.log(json);
  //     setObjQuestion(json);
  //     setCount(json.count);
  //     setFormData({...FormData, id_question: json.id_question})
  //   });
  // }
  //
  // function listQuestions(){
  //   fetch(getUrl("listQuestions.php"))
  //   .then(res => updateServerDate(res))
  //   .then(json => {
  //     console.log(json);
  //     console.log(json[1].ts_date);
  //     setLstQuestions(json);
  //   });
  // }
  //
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


  function showQuestionList(){
    return <div className="p-3">

    <table className="table-auto">
      <thead>
        <tr>
          <th>Date</th>
          <th>Status</th>
          <th>Text</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        {
          // <td className="text-sm text-red-700 px-2">{ServerDate ? timeAgo(new Date(q.ts_date), {now: ServerDate}) : q.ts_date.split(' ')[0]}</td>
          Array.isArray(LstQuestions) ? LstQuestions.map((q, index) =>
            <tr key={index} onClick={() => getQuestion(q.id)} className="cursor-pointer hover:bg-gray-200 px-2">
              <td className="text-sm text-red-700 px-2">{timeAgo(new Date(q.ts_date))}</td>
              <td className="px-2">{q.enm_status} </td>
              <td className="px-2">{q.id} - {q.txt_question}</td>
              <td className="px-2">
                {q.enm_status == "vote period"     ? <i className="fas fa-exclamation-circle" style={{width: "1em", margin:"auto", color: "green"}}></i> : ""}
                {q.enm_status == "proposal period" ? <i className="fas fa-check-circle"       style={{width: "1em", color: "red"}}></i>   : ""}
                {q.enm_status == "notify period"   ? <i className="fas fa-clock"              style={{width: "1em", color: "brown"}}></i> : ""}
                <i title="Choices available" className="fas fa-list-ol"></i>
                &nbsp;{q.choices} &nbsp;
                <i title="Comments Posted"   className="fas fa-comment-alt"></i>&nbsp;{q.comments} &nbsp;
                <i title="Total Votes"       className="fas fa-receipt"></i> &nbsp;{q.votes} &nbsp;
              </td>
            </tr>
          ) : <tr><td colSpan="3">No Questions Found</td></tr>
        }

      </tbody>
     </table>
    </div>
  }

  function showQuestionForm(){
    return <div>
      {ObjQuestion
        ? showQuestionForm()
        : ""}

      {showQuestionList()}
    </div>
  }


  function showDocument(){
    if(!Document) return "Not Ready";
    console.log(Document);

    function getBgClass(enm_condition){switch (enm_condition) {
      case 'removed'   : return "bg-gray-200";
      case 'modified'  : return "bg-gray-100";
      case 'accepted'  : return "bg-white";
      case 'disputed'  : return "bg-red-200";
      case 'considered': return "bg-gray-300";
    }	};

    function getTextClass(enm_condition){switch (enm_condition) {
      case 'removed'   : return "text-red-700 italic";
      case 'modified'  : return "";
      case 'accepted'  : return "";
      case 'disputed'  : return "italic";
      case 'considered': return "";
    }	}

    return <div>
        <p>Title: { Document[0].txt_name } </p>

        <table className="table-auto">
          <thead>
            <tr>
              <th>Info</th>
              <th>Text</th>
              <th>Actions</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {Document.map((entry,index) =>
              <tr key={index} className={getBgClass(entry.enm_condition)}>
                <td><i className="far fa-file"></i> <span className="text-xs">{entry.id} at {entry.ts_date} by {entry.id_user} ({entry.enm_condition})</span></td>
                <td className={getTextClass(entry.enm_condition)}>{entry.txt_element}</td>
                <td>
                  <span className="px-1 border cursor-pointer bg-blue-200 hover:bg-gray-300">[Dispute]</span>
                  <span className="px-1 border cursor-pointer bg-blue-200 hover:bg-gray-300">[View]</span>
                  <span className="px-1 border cursor-pointer bg-blue-200 hover:bg-gray-300">[History]</span>
                </td>
              </tr>)}

              <tr className="">
                <td className="text-xs"></td>
                <td className="">Create new Proposal for Appending</td>
                <td onClick={()=>alert("this should create a thing")}> <span className="px-1 border cursor-pointer bg-blue-200 hover:bg-gray-300">[Create]</span> </td>
              </tr>
          </tbody>
        </table>

      </div>
  }


  function showRoot(){
    return <div>hello im root</div>
  }


  // const timeAgo = new TimeAgo('en-US');
  console.log( (auth && auth.token) || "no token");
  const [Users, setUsers]               = useState(0);
  const [MainView, setMainView]         = useState((auth && auth.token) ? Overview : LoginLinks );

  // const [count, setCount]               = useState(0);
  const [VoteRes,      setVoteRes]      = useState({});
  const [FormData,     setFormData]     = useState({});
  const [ObjQuestion,  setObjQuestion]  = useState(null);
  const [LstQuestions, setLstQuestions] = useState({});
  const [ServerDate,   setServerDate]   = useState(null);
  const [Document,     setDocument]     = useState(null);

  useEffect(() => {
    // getDocument(1) .then(x => setDocument(x));
    // getAllUsers()  .then(x => setUsers(x));
    // listQuestions().then(x => setLstQuestions(x));
    if(auth && auth.token) {
      setMainView(Overview)
      localStorage.setItem("obj", JSON.stringify(auth));
    }
  }, [auth] );


  return (
      <div className="h-screen w-container justify-center items-start p-10 m-10">

        <Header />

        <div className="flex flex-row justify-center rounded overflow-hidden shadow-lg bg-gray-100 ">
          {MainView}
        </div>

        <div className="text-xs self-center absolute object-right-bottom bottom-0">
          {version.version} on {version.date}: {JSON.stringify(auth)}
        </div>
      </div>
  );
}


      // {showDocument()}
      // {showQuestionList()}
      // {showRoot()}

export default App;
