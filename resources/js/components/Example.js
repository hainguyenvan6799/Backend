import React from 'react';
import ReactDOM from 'react-dom';
import Form from './import_file/Form';
import ListUsers from './import_file/ListUsers';
import LoginForm from './login/LoginForm';
import Test from './Test';

function Example() {
    return (
        <div className="container">
            <div className="row justify-content-center">
                <div className="col-md-8">
                    <div className="card">
                        <div className="card-header">Example Component</div>

                        <div className="card-body">I'm an example component!</div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Example;

if (document.getElementById('example')) {
    ReactDOM.render(<Form />, document.getElementById('example'));
}

if(document.getElementById('list_users'))
{
    ReactDOM.render(<ListUsers />, document.getElementById('list_users'))
}

if(document.getElementById('login_form'))
{
    ReactDOM.render(<LoginForm />, document.getElementById('login_form'))
}

if(document.getElementById('test')){
    ReactDOM.render(<Test/>, document.getElementById("test"));
}