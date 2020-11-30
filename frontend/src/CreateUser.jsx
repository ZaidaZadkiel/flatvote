import React, { useState, useEffect, useRef } from 'react';
import {createUser, timeAgo} from './fetches.jsx';

const CreateUser = ({ObjQuestion}) => {
  const [FormData, setFormData] = useState(0);

  return CreateUserForm();

  function CreateUserForm(){
    return (
      <div>
        <div>Create user form</div>
        <table>
        <tbody>
          <tr>
            <td>Username:</td><td><input className="border" type="text" placeholder="username" onChange={(evt) => setFormData({...FormData, username: evt.target.value})}/></td>
          </tr>
          <tr>
            <td>Unsafe Password:</td><td><input className="border" type="text" placeholder="password" onChange={(evt) => setFormData({...FormData, password: evt.target.value})}/></td>
          </tr>
          <tr>
            <td colSpan="2">email should go here but its not</td>
          </tr>
          <tr>
            <td colSpan="2"><input className="w-full"
                                   type="submit"
                                   onClick={
                                     ()=>{
                                       createUser(FormData).then(
                                         x=>console.log(x)
                                       )}
                                     }/></td>
          </tr>
          </tbody>
        </table>
        {JSON.stringify(FormData)}
      </div>);
  }
};

export default CreateUser;
