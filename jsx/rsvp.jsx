import React from "react";
import ReactDom from "react-dom";
import Modal from 'react-modal';

let modalWindowStyle = {
    content : {
        top: '50%',
        left: '50%',
        right: 'auto',
        bottom: 'auto',
        transform: 'translate(-50%, -50%)',
        padding: 0
    }
};

class RsvpModal extends React.Component {
    _handleClick(e) {
        this.props.closeModal();
    }

    onRequestClose() {
        this.props.closeModal();
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <ul className="content rsvpSelector">
                        <li><p className="rsvpPart" onClick={this._handleClick.bind(this)}>御出席</p></li>
                        <li><p className="rsvpNonPart" onClick={this._handleClick.bind(this)}>御欠席</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class Rsvp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            rsvpModalDisplay: false
        };
    }

    openModal() {
        this.setState({
            rsvpModalDisplay: true
        });
    }

    closeModal() {
        this.setState({
            rsvpModalDisplay: false
        });
    }

    render() {
        return (
            <section>
                <div className="rsvp">
                    <button className="rsvpButton" onClick={this.openModal.bind(this)}>出欠のご回答</button>
                </div>
                <RsvpModal display={this.state.rsvpModalDisplay} closeModal={this.closeModal.bind(this)} />
            </section>
        );
    }
}

ReactDom.render(
    <Rsvp />,
    document.getElementById('container')
);