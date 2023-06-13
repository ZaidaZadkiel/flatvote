import * as React from 'react'
const AuthStateContext = React.createContext()
const AuthDispatchContext = React.createContext()

function authReducer(state, action) {
  // console.log(action);
  return action;

  // switch (action.type) {
  //   case 'increment': {
  //     return {auth: state.auth + 1}
  //   }
  //   case 'decrement': {
  //     return {auth: state.auth - 1}
  //   }
  //
  //   default: {
  //     throw new Error(`Unhandled action type: ${action.type}`)
  //   }
  // }
}

function AuthProvider({children}) {
  let authStorage = {auth:0};
  try {
    let x = localStorage.getItem("obj");
    if(typeof x === "string"){ x= JSON.parse(localStorage.getItem("obj")); }
    authStorage = x;
  } catch (e) {
    // console.error(e);
    localStorage.removeItem("obj");
  } //swallow

  const [state, dispatch] = React.useReducer(authReducer, authStorage);
  return (
    <AuthStateContext.Provider value={state}>
      <AuthDispatchContext.Provider value={dispatch}>
        {children}
      </AuthDispatchContext.Provider>
    </AuthStateContext.Provider>
  )
}

function useAuth() {
  return [useAuthState(), useAuthDispatch()]
}

function useAuthState() {
  // console.log("state");
  const context = React.useContext(AuthStateContext)
  if (context === undefined) {
    throw new Error('useAuthState must be used within a AuthProvider')
  }
  return context
}
function useAuthDispatch() {
  // console.log("dispatch");
  const context = React.useContext(AuthDispatchContext)
  if (context === undefined) {
    throw new Error('useAuthDispatch must be used within a AuthProvider')
  }
  return context
}
export {useAuth, AuthProvider, useAuthState, useAuthDispatch}
