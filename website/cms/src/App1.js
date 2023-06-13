import React, { useState, useEffect, useRef } from 'react';
import logo from './flatvote-logo.png';
import ReactDOM from "react-dom";
import { register } from "codelift";
// import TimeAgo from 'javascript-time-ago'
import {getDocument, getQuestion, getAllUsers, listQuestions, timeAgo} from './fetches.jsx';
import ViewVote from './ViewVote.jsx';
import CreateUser from './CreateUser.jsx';



register({ React, ReactDOM });
const version = require("./version.json");

function App() {
  const [Users, setUsers]               = useState(0);
  const [MainView, setMainView]         = useState(<CreateUser/>);

  return (
    <div className="h-screen w-screen flex justify-center items-start -m-1">
    <div className="text-xs self-center absolute object-right-bottom bottom-0"> {version.version} on {version.date}</div>

    <div className="rounded overflow-hidden shadow-lg w-screen flex flex-col p-8 mx-10">
      <div className="inline-flex">
        <img className="w-16" src={logo} alt="Sunset in the mountains" />
        <span className="font-bold text-xl ml-8 self-center"> FlatVote Radical Democracy Software&nbsp;</span>
      </div>

      <div className="px-6 pb-4 ">
        {["React", "Tailwind"].map(tag =>
              <span
                key={tag}
                className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mt-2">
                {"#" + tag }
              </span>
        )}
        user:
        <select>
        { Users && Array.isArray(Users)
            ? (Users.length==0
                ? <option value="coconut">---</option>
                : Users.map((user, index)=> <option key={index} value={user.id}>{user.name}</option>))
            : <option value="coconut">nothing</option>
        }
        </select>
        <span className="mx-4 px-2 cursor-pointer bg-blue-200 hover:bg-gray-300" onClick={() => setMainView(
        <CreateUser/>)}>Create new User</span>
      </div>

      {MainView ? MainView : "nope" }
    </div>
  </div>
  );
}

export default App;
