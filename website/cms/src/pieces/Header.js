import React, { memo } from 'react';
import logo from '../flatvote-logo.png';
import {useAuth} from '../authContext';

function Header({ label, score = 0, total = Math.max(1, score) }) {
  const [auth, setAuth] = useAuth();

  return (
    <div className="rounded overflow-hidden shadow-lg mb-10">
      <div className="inline-flex">
        <img className="w-16" src={logo} alt="Sunset in the mountains" />
        <span className="font-bold text-xl ml-8 self-center"> FlatVote Radical Democracy Software&nbsp;</span>
      </div>


      <div className="px-6 pb-4 ">
        <span
        className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mt-2"
        >
        {(auth && auth.profile.username ) || "nope" }
        </span>
        {["React", "Tailwind"].map(
          (tag) => (
            <span
              key      ={tag}
              className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mt-2">
              {"#" + tag }
            </span>
          )
        )}
      </div>
    </div>
  )
}

// Wrap component using `React.memo()`
export default memo(Header);
