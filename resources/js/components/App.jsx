import React from 'react';
import PropTypes from 'prop-types';
import ReactDOM from 'react-dom/client';
import App from '../app';
App.propTypes = {

};

function App(props) {
    return (
        <div>

        </div>
    );
}

export default App;
if (document.getElementById('root')) {
    const Index = ReactDOM.createRoot(document.getElementById("root"));

    Index.render(
        <React.StrictMode>
            <App/>
        </React.StrictMode>
    )
}
