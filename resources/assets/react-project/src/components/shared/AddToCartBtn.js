import React, {Component} from 'react';


class AddToCartBtn extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <a id={this.props.id} onClick={this.props.clickHandler()}>Add</a>
        )
    }
}

export default AddToCartBtn;
