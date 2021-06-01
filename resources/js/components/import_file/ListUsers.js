import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

export default class ListUsers extends Component
{
	constructor(props){
		super(props);
		this.state = {
			users: []
		}
	}
	componentDidMount()
	{
		axios.get('http://127.0.0.1:8000/user/get_list_users')
		.then(response => {
			this.setState({
				users: response.data
			})
		}).catch(error => console.log(error))
	}
	render(){
		return (
			<div className="container-fluid">
				<table className="table">
					<thead>
						<tr>
							<th>STT</th>
							<th>Tên</th>
							<th>Password</th>
						</tr>
					</thead>
					<tbody>
						{
							this.state.users.map((user, index) => {
								return (
									<tr key={index}>
										<td>{index}</td>
										<td>{user.name}</td>
										<td>{user.password}</td>
									</tr>
								)
							})
						}
						
					</tbody>
				</table>
			</div>
		)
	}
}