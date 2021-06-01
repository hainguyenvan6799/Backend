import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import Form from '../import_file/Form';


export default class LoginForm extends Component{
	constructor(props){
		super(props);
		this.state = {
			'user_name': '',
			'password': ''
		}
		this.onSubmitForm = this.onSubmitForm.bind(this);
		this.onChangeInput = this.onChangeInput.bind(this);
	}

	onChangeInput(event)
	{
		this.setState({
			[event.target.name]: event.target.value
		})
		console.log(this.state);
	}

	onSubmitForm(event){
		event.preventDefault();
		let formData = new FormData;
		formData.append('email', this.state.user_name);
		formData.append('password', this.state.password);
		axios.post('http://127.0.0.1:8000/login', formData).then(response => console.log(response)).catch(error => console.log(error))
	}

	render(){
		return (
			<div className="container">
				<form onSubmit={(event) => this.onSubmitForm(event)}>
					<input type="text" name="user_name" id="user_name" onChange={(event) => this.onChangeInput(event)} value={this.state.user_name} />
					<input type="password" name="password" id="password" onChange={(event) => this.onChangeInput(event)} value={this.state.password} />
					<button className="btn btn-primary">Login</button>
				</form>
				<Form/>
			</div>
		)
	}
}