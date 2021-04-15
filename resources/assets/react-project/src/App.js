import React, {Component} from 'react';
import {BrowserRouter as Router, Switch, Route} from 'react-router-dom';
import Catalogue from './components/features/Catalogue';
import Products from './components/features/Products';
import ProductForm from './components/features/ProductForm';
import Cart from './components/features/Cart';
import Orders from './components/features/Orders';
import Order from './components/features/Order';
import Login from './components/features/Login';
import './App.css';

class App extends Component {
    render() {
        return (
            <Router>
                <div className="App">
                    <Switch>
                        <Route path={'/'} exact component={Catalogue} />
                        <Route path={'/login'} exact component={Login} />
                        <Route path={'/products'} exact component={Products} />
                        <Route path={'/products/create'} exact component={ProductForm} />
                        <Route path={'/products/:id/edit'} component={ProductForm} />
                        <Route path={'/cart'} component={Cart} />
                        <Route path={'/orders'} exact component={Orders} />
                        <Route path={'/orders/:id'} component={Order} />
                    </Switch>
                </div>
            </Router>
        );
    }
}

export default App;
