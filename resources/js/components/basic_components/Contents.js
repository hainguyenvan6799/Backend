import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router, Link, Route } from "react-router-dom";
import LoginForm from '../login/LoginForm';

export default class Contents extends Component {
    render() {
        return (
            <div className="col-md-12 bg-primary">
                <Route exact path="/signin" component={LoginForm} />
            </div>
        )
    }
}