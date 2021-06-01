import React, {Component} from 'react';

export default class Test extends Component
{
    constructor(){
        this.open = this.open.bind(this);
    }
    open()
    {
        alert("Tôi đã bấm nút này.");
    }
    render(){
        return (
            <div className="container">
                <h2>Test</h2>
                <button onClick={this.open}></button>
            </div>
        )
    }
}