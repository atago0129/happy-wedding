import React from "react";
import ReactDom from "react-dom";
import Modal from 'react-modal';

class Item extends React.Component {
    _handleClick(e) {
        this.props.openModal("modal_" + this.props.itemKey);
    }

    render() {
        return (
            <li>
                <a href="javascript: void(0)" onClick={this._handleClick.bind(this)}>
                    <figure><img src={this.props.image} /></figure><p className="name">{this.props.name}</p>
                </a>
            </li>
        )
    }
}

class List extends React.Component {
    render() {
        var _this = this;
        return (
            <ul className="itemList">
                {this.props.itemList.map(function(item) {
                    return <Item key={item.key} name={item.name} image={item.image} itemKey={item.key} openModal={_this.props.openModal} />
                })}
            </ul>
        )
    }
}

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

class ItemModalWindow extends React.Component {
    clickSelect(e) {
        this.props.closeModal(this.props.modalKey);
        this.props.openConfirm("confirm_" + this.props.itemData.key);
    }

    clickCancel(e) {
        this.props.closeModal(this.props.modalKey);
    }

    onRequestClose() {
        this.props.closeModal(this.props.modalKey);
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <h1 className="modalTitle">{this.props.itemData.name}</h1>
                    <p className="modalDescription">{this.props.itemData.description}</p>
                    <img className="modalImage" src={this.props.itemData.image} />
                    <ul className="modalSelectButtons">
                        <li><p onClick={this.clickSelect.bind(this)}>これにする</p></li>
                        <li><p onClick={this.clickCancel.bind(this)}>キャンセル</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class ConfirmModalWindow extends React.Component {
    _handleClick(e) {
        this.props.closeModal(this.props.confirmKey);
    }

    onRequestClose() {
        this.props.closeModal(this.props.confirmKey);
    }

    render() {
        return (
            <Modal isOpen={this.props.display}
                   onRequestClose={this.onRequestClose.bind(this)}
                   style={modalWindowStyle}
            >
                <div>
                    <h1 className="modalTitle">確認</h1>
                    <p className="modalDescription">以下の商品で確定します。よろしいですか？</p>
                    <h2 className="modalDescription">{this.props.itemData.name}</h2>
                    <ul className="modalSelectButtons">
                        <li><p onClick={this._handleClick.bind(this)}>キャンセル</p></li>
                        <li><p onClick={this._handleClick.bind(this)}>確定</p></li>
                    </ul>
                </div>
            </Modal>
        );
    }
}

class App extends React.Component {
    constructor(props) {
        super(props);
        this.initializeModal();
    }

    initializeModal() {
        var modalDisplay = {};
        var confirmDisplay = {};
        this.props.itemList.map(function(item) {
            modalDisplay["modal_" + item.key] = false;
            confirmDisplay["confirm_" + item.key] = false;
        });
        this.state = {
            modalDisplay: modalDisplay,
            showModalKey: null,
            confirmDisplay: confirmDisplay,
            confirmKey: null
        }
    }

    openItemModal(modalKey) {
        var modalDisplay = this.state.modalDisplay;
        modalDisplay[modalKey] = true;
        this.setState({
            modalDisplay: modalDisplay,
            showModalKey: modalKey
        });
    }

    closeItemModal(modalKey) {
        var modalDisplay = this.state.modalDisplay;
        modalDisplay[modalKey] = false;
        this.setState({
            modalDisplay: modalDisplay,
            showModalKey: null
        });
    }

    openConfirmModal(confirmKey) {
        var confirmDisplay = this.state.confirmDisplay;
        confirmDisplay[confirmKey] = true;
        this.setState({
            confirmDisplay: confirmDisplay,
            confirmKey: confirmKey
        });
    }

    closeConfirmModal(confirmKey) {
        var confirmDisplay = this.state.confirmDisplay;
        confirmDisplay[confirmKey] = false;
        this.setState({
            confirmDisplay: confirmDisplay,
            confirmKey: null
        });
    }

    closeOnBackGround(e) {
        var targetStyle = e.target.getAttribute('style');
        if (targetStyle !== null && targetStyle.indexOf('z-index: 10000;') !== -1) {
            var modalDisplay = this.state.modalDisplay;
            if (this.state.showModalKey) {
                modalDisplay[this.state.showModalKey] = false;
            }
            var confirmDisplay = this.state.confirmDisplay;
            if (this.state.confirmKey) {
                confirmDisplay[this.state.confirmKey] = false;
            }
            this.setState({
                modalDisplay: modalDisplay,
                showModalKey: null,
                confirmDisplay: confirmDisplay,
                confirmKey: null
            });
        }
    }

    render() {
        var _this = this;
        return (
            <section id="mainBody" onClick={this.closeOnBackGround.bind(this)}>
                <List itemList={this.props.itemList} openModal={this.openItemModal.bind(this)} />
                {this.props.itemList.map(function(item) {
                    var key = "modal_" + item.key;
                    return <ItemModalWindow
                        key={key}
                        display={_this.state.modalDisplay[key]}
                        itemData={item}
                        modalKey={key}
                        closeModal={_this.closeItemModal.bind(_this)}
                        openConfirm={_this.openConfirmModal.bind(_this)}
                    />;
                })}
                {this.props.itemList.map(function(item) {
                    var key = "confirm_" + item.key;
                    return <ConfirmModalWindow
                        key={key}
                        confirmKey={key}
                        display={_this.state.confirmDisplay[key]}
                        closeModal={_this.closeConfirmModal.bind(_this)}
                        itemData={item} />
                })}
            </section>
        );
    }
}

var data = JSON.parse(document.getElementById('listDataProvider').getAttribute('data-list'));
ReactDom.render(
    <App itemList={data} />,
    document.getElementById('container')
);

