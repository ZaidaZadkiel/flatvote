import React, { useState, useEffect, useRef } from 'react';
import {loginUser, timeAgo} from './fetches.jsx';
import {useAuth} from './authContext';

const LoginUser = ({ObjQuestion}) => {
  const [auth, setAuth]         = useAuth();
  const [FormData, setFormData] = useState({});
  const [Result,   setResult]   = useState({});

  return LoginUserForm();

  function LoginUserForm(){
    return (
      <div>
        <div>Login User</div>

        <table>
        <tbody>
          <tr>
            <td>Username:</td>
            <td><input
              className="border"
              type="text"
              placeholder="username"
              onChange={(evt) => setFormData({...FormData, username: evt.target.value})}
            /></td>
          </tr>
          <tr>
            <td>Unsafe Password:</td>
            <td><input
              className="border"
              type="text"
              placeholder="password"
              onChange={(evt) => setFormData({...FormData, password: evt.target.value})}
            /></td>
          </tr>
          <tr>
            <td colSpan="2">
              <input className="w-full" type="submit"
                     onClick={
                       ()=>{
                         loginUser(FormData, auth)
                          .then(x=>{
                             console.log(x);
                             setAuth(x);
                       })}
                       }/></td>
          </tr>
          </tbody>
        </table>
        {JSON.stringify(FormData)}<br/>
        {JSON.stringify(Result)}<br/>
        {""+JSON.stringify(auth)}
      </div>);
  }
  // <button onClick={()=>setAuth({type:"increment"})}>lel</button>

};

export default LoginUser;
