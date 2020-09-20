import Token from './Token.js'
import AppStorage from './AppStorage.js'

class User {

    login(credentials){

        axios.post('/api/login',credentials)
            .then( res=>this.responseAfterLogin(res) )
            .catch(error=>alert('Something went wrong, Please try again'))
    }

    responseAfterLogin(res){

        if (Token.isValid(res.data.access_token))
            AppStorage.storeCredentials(res.data.access_token, res.data.user)

    }

    hasToken(){

        const storedToken = AppStorage.getToken()
        if (storedToken){
            return Token.isValid(storedToken)? true:false
        }
        return false
    }

    loggedIn(){ return this.hasToken() }

    logout() { AppStorage.clearCredentials() }

    userData(){ return AppStorage.getUser() }
}

export default User = new User()