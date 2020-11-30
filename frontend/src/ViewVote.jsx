import React, { useState, useEffect, useRef } from 'react';
import {castVote, timeAgo} from './fetches.jsx';

const ViewVote = ({ObjQuestion}) => {
  const [VoteRes, setVoteRes]           = useState({});
  const [FormData, setFormData]         = useState({});
  const [ServerDate, setServerDate]     = useState(null);
  const [count, setCount]               = useState(0);



  function showVotingForm(ObjQuestion){
    if(!ObjQuestion) return "ViewVote: Nothing here "+JSON.stringify(ObjQuestion) ;

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
                  <p className="text-xs">{timeAgo(new Date(r.timestamp))}</p>
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

  // <p className="text-xs">{ServerDate ? TimeAgo.format(new Date(r.timestamp), {now: ServerDate}) : r.timestamp}</p>

  return showVotingForm(ObjQuestion);
};

export default ViewVote;
