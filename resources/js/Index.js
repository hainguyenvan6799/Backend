import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import BasicComponents from './components/basic_components/Index';
import {BrowserRouter as Router, Link, Route} from 'react-router-dom';

export default class TrangChu extends Component{
    render(){
        return (
            <BasicComponents/>
        )
    }
} 

const root = document.querySelector("#root");
if(root)
{
    ReactDOM.render(<Router><TrangChu/></Router>, root);
}