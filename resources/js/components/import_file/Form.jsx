import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import ErrorsAlert from './ErrorsAlert';

export default class Form extends Component{

    constructor(props){
        super(props);
        this.state = {
            files: {},
            errors: []
        }
        this.onChangeForm = this.onChangeForm.bind(this);
        this.onSubmitForm = this.onSubmitForm.bind(this);
    }

    onChangeForm(event){
        this.setState({
            files: event.target.files
        })
    }

    onSubmitForm(event){
        event.preventDefault();
        let file = this.state.files;
        let file_extension = file[0].name.split(".")[1];
        if(file_extension != "xlsx")
        {
            this.setState({
                errors: "Bạn chỉ được nhập file Excel."
            })
        }
        else
        {
            let formData = new FormData;
            formData.append('file', file[0]);
            axios.post("http://127.0.0.1:8000/user/import", formData).then(response =>{
                // response.data.map(item => console.log(item.errorInfo[2]));
                // console.log(response.data);
                this.setState({
                    files: {},
                    errors: response.data
                })
            }).catch(error => console.log(error))
        }
        
    }

    render(){
        return (
            <form onSubmit={(event) => this.onSubmitForm(event)}>
            <ErrorsAlert errors={this.state.errors}/>
                <input type="file" onChange={(event) => this.onChangeForm(event)} />
                <button className="btn btn-primary">Submit</button>
            </form>
        )
    }
}