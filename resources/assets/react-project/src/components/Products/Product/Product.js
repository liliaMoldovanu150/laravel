import React, {Component} from "react";
import AddToCartBtn from "../../AddToCartBtn/AddToCartBtn";
import './Product.css';
import axios from "axios";

axios.defaults.xsrfHeaderName = "X-CSRFToken";

class Product extends Component {
    constructor(props) {
        super(props);
        this.state = {
            id: ''
        }
    }

    handleAddToCartBtn = (e) => {
        e.preventDefault();
        const id = e.target.getAttribute('id');
        this.setState({id: id});
        axios.post(`http://127.0.0.1:8000/cart/${this.state.id}`, this.state.id)
            .then(res => {
                const products = res.data;
                this.setState({products});
            })
    }

    render() {
        let imageSrc = 'http://localhost:8000/images/' + this.props.image;
        return (
            <div className="product-item">
                <div className="product-image">
                    <img src={imageSrc} alt="product_image"/>
                </div>
                <div className="product-features">
                    <div>{this.props.title}</div>
                    <div>{this.props.description}</div>
                    <div>{this.props.price}</div>
                </div>
                <AddToCartBtn id={this.props.id} clickHandler={() => this.handleAddToCartBtn}/>
            </div>
        )
    }
}

export default Product;
