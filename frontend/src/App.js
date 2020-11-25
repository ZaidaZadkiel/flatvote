import React, { useState, useEffect, useRef } from 'react';
import logo from './flatvote-logo.png';
import ReactDOM from "react-dom";
import { register } from "codelift";
import TimeAgo from 'javascript-time-ago'

// English.
import en from 'javascript-time-ago/locale/en'

TimeAgo.addDefaultLocale(en)


register({ React, ReactDOM });
const version = require("./version.json");
function App() {

  const timeAgo = new TimeAgo('en-US');

  const [count, setCount]               = useState(0);
  const [VoteRes, setVoteRes]           = useState({});
  const [FormData, setFormData]         = useState({});
  const [ObjQuestion, setObjQuestion]   = useState(null);
  const [LstQuestions, setLstQuestions] = useState({});
  const [ServerDate, setServerDate]     = useState(null);
  const [Document, setDocument]         = useState(null);

  useEffect(() => getDocument(1), [true] );


  function getUrl(path){console.log("http://zaidazadkiel.com/flatvote/db/"+path); return "http://zaidazadkiel.com/flatvote/db/"+path }
  function debugUrl(path){return process.env.NODE_ENV == "development" ? "https://cors-anywhere.herokuapp.com/" : "" }
  function castVote(){getQuestion()}

  function updateServerDate(res){
    var x;
    for (var pair of res.headers.entries()) {
       console.log(pair[0]+ ': '+ pair[1]);
       if(pair[0] =="timestamp") x = pair[1];
    }
    if(x) {
      console.log("now date", x)
      setServerDate(new Date(x));
    }
    return res.json();
  }

  function getDocument(id){
    fetch(getUrl(`getDocument.php?id=${id}`))
    .then(res => updateServerDate(res))
    .then(json => {
      console.log(json);
      setDocument(json);
    });
    listQuestions();
  }

  function getQuestion(question){
    fetch(getUrl(`getQuestion.php?id=${question}`))
    .then(res => updateServerDate(res))
    .then(json => {
      console.log(json);
      setObjQuestion(json);
      setCount(json.count);
      setFormData({...FormData, id_question: json.id_question})
    });
  }

  function listQuestions(){
    fetch(getUrl("listQuestions.php"))
    .then(res => updateServerDate(res))
    .then(json => {
      console.log(json);
      console.log(json[1].ts_date);
      setLstQuestions(json);
    });
  }

  function castVote(vote){
    console.table(FormData);

    fetch(debugUrl()+getUrl("castVote.php"),
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
    .then(json => {console.table(json); setVoteRes(json); if(json.count) setCount(json.count) });
  }


  function showVotingForm(){
    return <div className="flex flex-col  px-4 h-full">
      <div className="flex-col self-center mb-4">Question {ObjQuestion.id_question} on {ObjQuestion.ts_date_question} (status: {ObjQuestion.enm_status})</div>
        <div className="flex mb-4 overflow-hidden ">

          <div className="lg:w-1/5 border">
            <div className="overflow-hidden text-pink-900 text-xs italic px-1 ">
              User Activity
              <ul>
                <li>12/23/34 user activity
                </li>
              </ul>
            </div>
          </div>

          <div className="flex-grow px-4">
            <div className="inline-grid text-justify w-full  text-center">
            Voting for: <span className="text-xl ml-8 self-center">{ObjQuestion.txt_question}</span>
              <label className="inline-flex items-center">
                user id: <input type="text" className="border" onChange={(e)=>setFormData({...FormData, id_user:e.target.value}) }/>
              </label>
              {ObjQuestion.options &&  ObjQuestion.options.map(option =>
                <label key={option.id} className="inline-flex items-center">
                  <input type="radio" className="form-radio" name="accountType" onChange={()=>setFormData({...FormData, id_ballot:option.id})} />
                  <span className="ml-2">{option.txt_description}</span>
                </label>
              )}

              <label className="inline-flex items-center">
                comment:<input type="text" className="border" maxLength="200" onChange={(e)=>setFormData({...FormData, txt_comment:e.target.value}) }/>
              </label>
              <div><input onClick={()=>castVote()} type="submit" value="Cast Vote"/></div>
            </div>
            {(VoteRes.error
              ?
                <div className="px-1 text-red-700 text-sm text-center">
                  {JSON.stringify(VoteRes)}
                </div>
              : "" )}
              <div className="px-1 text-sm text-center">
                {count} Votes from 100 Collaborators<br/>
            </div>
          </div>

          <div className="lg:w-1/5 w-1/5 border">
            <div className="text-sm text-grey-dark">
              {Array.isArray(ObjQuestion.comments) ? ObjQuestion.comments.map((r,index) =>
                <div key={index}>
                  <p className="text-xs">{ServerDate ? timeAgo.format(new Date(r.timestamp), {now: ServerDate}) : r.timestamp}</p>
                  {r.txt_comment} -&nbsp;
                  <span className="text-xs text-green-700">
                    {ObjQuestion.options.find(option => option.id == r.id_ballot).txt_description}
                  </span>
                </div>
              ) : ""}
              <p>

              </p>
            </div>
          </div>
      </div>
    </div>
  }

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
          Array.isArray(LstQuestions) ? LstQuestions.map((q, index) =>
            <tr key={index} onClick={() => getQuestion(q.id)} className="cursor-pointer hover:bg-gray-200 px-2">
              <td className="text-sm text-red-700 px-2">{ServerDate ? timeAgo.format(new Date(q.ts_date), {now: ServerDate}) : q.ts_date.split(' ')[0]}</td>
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
        <p>Title: { Document[0].txt_name }</p>

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
                <td className="text-xs">{entry.id} at {entry.ts_date} by {entry.id_user} ({entry.enm_condition})</td>
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

  return (
    <div className="h-screen w-screen flex justify-center items-start -m-1">
    <div className="text-xs self-center absolute object-right-bottom bottom-0"> {version.version} on {version.date}</div>

    <div className="rounded overflow-hidden shadow-lg w-screen flex flex-col p-8 mx-10">
      <div className="inline-flex">
        <img className="w-16" src={logo} alt="Sunset in the mountains" />
        <span className="font-bold text-xl ml-8 self-center"> FlatVote Radical Democracy Software&nbsp;</span>
      </div>

      <div className="px-6 pb-4">
        {["React", "Tailwind"].map(tag =>
              <span
                key={tag}
                className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mt-2">
                {"#" + tag }
              </span>
        )}
      </div>

      {showDocument()}
      {showQuestionList()}

    </div>
  </div>
  );
}

export default App;
