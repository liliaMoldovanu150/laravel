import React, {Component} from 'react';
import axios from "axios";
import Product from "../shared/Product";

class Catalogue extends Component {
    constructor(props) {
        super(props);
        this.state = {
            products: [],
        };
    }

    componentDidMount() {
        axios.get('http://127.0.0.1:8000')
            .then(res => {
                const products = res.data;
                this.setState({ products });
            })
    }

    render() {
        const products = this.state.products;
        let pageContent = 'All items added to cart'
        if (products.length) {
            pageContent = products.map(function(product, index){
                return <Product
                    key={ index }
                    id={product.id}
                    image={product['image_url']}
                    title={product.title}
                    description={product.description}
                    price={product.price}
                />;
            })
        }

        return (
            <div>
                {pageContent}
            </div>
        )
    }
}

export default Catalogue;
