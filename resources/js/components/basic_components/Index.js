import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import Header from './Header';
import Contents from './Contents';
import Footer from './Footer';
import Navbar from './Navbar';

export default class BssicComponents extends Component{
    render(){
        return (
            <div className="container-fluid">
                <Header/>
                <Navbar/>
                <Contents/>
                <Footer/>
            </div>
        )
    }
}