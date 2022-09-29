import logo from './logo.svg';
import './App.css';
import React from 'react';
import axios from 'axios';

class DictionaryValue extends React.Component {
  constructor(props) {
    super(props);
    this.state = { visible: false };
  }

  reset = () => { this.setState({ visible: false }); }

  render() {
    if(this.state.visible) {
      return <div className="mt-4 d-none">{this.props.value}</div>
    }

    return <div className='mt-4'>
      { React.createElement (
        'button',
        { onClick: () => this.setState({ visible: true }) },
        this.props.button
      )}
    </div>
  }
}

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = { word: '', transcription: '', translation: '' };
    this.transcription = React.createRef();
    this.translation = React.createRef();
  }

  setnewword = () => {
    axios.get('https://pr-diz.ru/chinese/word.php')
    .then(res => {
      const dictionaryValue = res.data;
      this.setState({ word: res.data });
      //this.setState({ dictionaryValue });
      //this.setState({ word: 'ASd', transcription: 'ASD', translation: 'NJU' });
    })
    // this.setState({ word: "CDE" });
    //this.setState({"word":"\u7167\u5f20\u76f8","transcription":"zh\u00e0o zh\u0101ng xi\u00e0ng","translation":"\u0441\u0434\u0435\u043b\u0430\u0442\u044c \u0441\u043d\u0438\u043c\u043e\u043a, c\u0444\u043e\u0442\u043e\u0433\u0440\u0430\u0444\u0438\u0440\u043e\u0432\u0430\u0442\u044c "});
    
    this.transcription.current.reset();
    this.translation.current.reset();
  }

  componentDidMount() {
    this.setnewword();
  }

  render() {
    return <div className="App">
    <div className="text-center">
      { React.createElement(
        'button',
        { onClick: () => this.setnewword() },
        "Новое слово"
      )}
    </div>
    <hr />
    <h1>{ this.state.word }</h1>
    <DictionaryValue ref={this.transcription} value={this.state.transcription} button="Показать транскрипцию" />
    <DictionaryValue ref={this.translation} value={this.state.translation} button="Показать перевод" />
  </div>
  }
}

export default App;