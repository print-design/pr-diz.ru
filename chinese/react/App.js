import logo from './logo.svg';
import './App.css';
import React from 'react';

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
    this.state = { original: this.props.original, transcription: this.props.transcription, translation: this.props.translation };
    this.transcription = React.createRef();
    this.translation = React.createRef();
  }

  render() {
    return <div className="App">
    <div className="text-center">
      { React.createElement(
        'button',
        { onClick: () => {
          this.setState({ original: "qwe", transcription: "rty", translation: "uio" });
          this.transcription.current.reset();
          this.translation.current.reset();
        }},
        "Новое слово"
      )}
    </div>
    <hr />
    <h1>{this.state.original}</h1>
    <DictionaryValue ref={this.transcription} value={this.state.transcription} button="Показать транскрипцию" />
    <DictionaryValue ref={this.translation} value={this.state.translation} button="Показать перевод" />
  </div>
  }
}

export default App;