import './App.css';
import React from 'react';
import axios from 'axios';

class DictionaryItem extends React.Component {
  constructor(props) {
    super(props);
    this.state = { visible: false };
  }

  reset = () => { this.setState({ visible: false }); }

  render() {
    if(this.state.visible) {
      return <div className="mt-4">{this.props.value}</div>
    }

    return <div className='mt-4'>
      { React.createElement (
        'button',
        { onClick: () => this.setState({ visible: true }), className: 'mt-4 btn btn-outline-dark' },
        this.props.button
      )}
    </div>
  }
}

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = { dictionaryItem: [] };
    this.transcription = React.createRef();
    this.translation = React.createRef();
  }

  setnewword = () => {
    var dataservice = `http://localhost/pr-diz.ru/chinese/word.php`;

    if(window.dataservice != null) {
      dataservice = window.dataservice;
    }

    axios.get(dataservice)
    .then(res => {
      const dictionaryItem = res.data;
      this.setState({ dictionaryItem });
      this.transcription.current.reset();
      this.translation.current.reset();
    })
  }

  componentDidMount() {
    this.setnewword();
  }

  render() {
    return <div className="App">
    <div className="text-center">
      { React.createElement(
        'button',
        { onClick: () => this.setnewword(), className: 'btn btn-dark mt-2' },
        "Новое слово"
      )}
    </div>
    <hr />
    <h1>{ this.state.dictionaryItem.word }</h1>
    <DictionaryItem ref={this.transcription} value={ this.state.dictionaryItem.transcription } button="Показать транскрипцию" />
    <DictionaryItem ref={this.translation} value={ this.state.dictionaryItem.translation } button="Показать перевод" />
  </div>
  }
}

export default App;